<?php

namespace Blog\Modules\User;

use Blog\Modules\DateFormat\DateFormat;

class User
{
    use Components\UserAuthMethods;

    public const ACCESS_LEVEL_ANONYM = 1;
    public const ACCESS_LEVEL_ALL = 2;
    public const ACCESS_LEVEL_USER = 3;
    public const ACCESS_LEVEL_ADMIN = 4;
    public const ACCESS_LEVEL_MASTER = 5;
    public const SESSUID = 'user-session';
    public const LOGID = 'user';
    public const TBL_STATUSES = 'users_statuses';
    public const TBL_STATUS_ACCESS = 'users_status_access_levels';

    protected array $status_list = [];
    protected array $access_levels = [];
    protected array $default_status;
    protected bool $initialized = false;
    protected bool $authorized = false;
    protected Token $token;
    protected UserAgent $agent;
    protected ?string $utoken;
    protected array $opened_sessions;

    public function __construct()
    {
        $this->prepareSession()
            ->preloadData();

        return $this;
    }

    protected function prepareSession(): self
    {
        if (!session()->isset(self::SESSUID)) {
            session()->set(self::SESSUID, []);
        }

        return $this;
    }

    protected function preloadData(): self
    {
        foreach ($this->getStorageData() as $row) {
            // set status list
            if (!isset($this->status_list[$row['status']])) {
                $this->status_list[$row['status']] = [
                    'id' => $row['usid'],
                    'status' => $row['status'],
                    'label' => $row['status_label']
                ];
            }
            // set access level data
            if (!isset($this->access_levels[$row['alid']])) {
                $this->access_levels[$row['alid']] = [
                    'id' => $row['alid'],
                    'label' => $row['label'],
                    'allow' => []
                ];
            }
            // add allowed user status to access level data
            $this->access_levels[$row['alid']]['allow'][$row['usid']] = $row['status'];
        }
        // get defaul user status id
        $key = arraySearchFirstKeyByColumn($this->status_list, self::ACCESS_LEVEL_ANONYM, 'id');
        // store defaul user status data
        $this->default_status = $this->status_list[$key];
        return $this;
    }

    protected function getStorageData(): array
    {
        return sql_select(from: ['al' => 'access_levels'])
            ->join(table: ['als' => self::TBL_STATUS_ACCESS], using: 'alid')
            ->join(table: ['us' => self::TBL_STATUSES], using: 'usid')
            ->columns([
                'al' => ['alid', 'label'],
                'us' => ['usid', 'status', 'status_label' => 'label']
            ])->order(['als.alid', 'als.usid'])
            ->all();
    }
    
    public function token(): Token
    {
        if (!isset($this->token)) {
            $this->token = new Token;
        }
        return $this->token;
    }

    public function agent(): UserAgent
    {
        if (!isset($this->agent)) {
            $this->agent = new UserAgent;
        }
        return $this->agent;
    }

    protected function status(?string $status = null): \stdClass
    {
        return isset($this->status_list[$status]) ? (object)$this->status_list[$status] : (object)$this->status_list;
    }

    protected function accessList(): array
    {
        return $this->access_levels;
    }

    public function initialize(): self
    {
        if (!$this->initialized) {
            $this->setUserStatus();
            $this->initialized = true;
        }
        return $this;
    }

    protected function setUserStatus(): self
    {
        if ($this->token()->utoken() || $this->token()->getCookieUToken()) {
            systemLog(self::LOGID, 'User token exists. Verifying User session.');
            $this->verifyUserSession();
        } else {
            systemLog(self::LOGID, 'There is no User token. Setting default User status.');
            $this->setDefaultStatus();
        }
        return $this;
    }

    protected function verifyUserSession(): self
    {
        if ($this->token()->verifySessionTokenTimeout()) {
            systemLog(self::LOGID, 'User token verification doesn\'t timed out. Validating User status.');
        } else if ($this->token()->verify()) {
            systemLog(self::LOGID, 'User token verified successfully. Validating User status.');
        } else {
            systemLog(self::LOGID, 'User token verification failed. Setting default User status.');
            return $this->setDefaultStatus();
        }
        $this->verifyUserStatus();
        return $this;
    }

    protected function verifyUserStatus(): self
    {
        $s = session()->get(self::SESSUID . '/status');
        if (!isset($s, $s['id'], $s['status'], $s['label'])) {
            systemLog(self::LOGID, 'There is no valid User status. Setting default User status.');
            return $this->setDefaultStatus();
        }
        $us = $this->status_list[$s['status']] ?? null;
        if (
            empty($us)
            || ($us['id'] ?? null) != $s['id']
            || ($us['status'] ?? null) != $s['status']
            || ($us['label'] ?? null) != $s['label']
        ) {
            systemLog(self::LOGID, 'Invalid User status. Setting default User status.');
            return $this->setDefaultStatus();
        }
        systemLog(self::LOGID, 'User status is valid.');
        $this->authorized = true;
        return $this;
    }

    protected function setDefaultStatus(): self
    {
        session()->set(self::SESSUID . '/status', $this->default_status);
        session()->set(self::SESSUID . '/token', null);
        session()->set(self::SESSUID . '/udata', null);
        $this->utoken = null;
        if (cookies()->isset(Token::COOKIE_USER_TOKEN)) {
            cookies()->unset(Token::COOKIE_USER_TOKEN);
        }
        $this->authorized = false;
        return $this;
    }

    public function getUserStatusId(): int
    {
        $this->initialize();
        return (int)$this->getUserStatus()->id;
    }

    public function getUserStatus(): \stdClass
    {
        $this->initialize();
        return (object)session()->get(self::SESSUID . '/status');
    }

    public function verifyAccessLevel(int $level): bool
    {
        $this->initialize();
        if (!isset($this->access_levels[$level])) {
            return false;
        }
        return in_array(
            $this->getUserStatus()->status,
            $this->access_levels[$level]['allow']
        );
    }

    public function logout(): self
    {
        $this->initialize();
        $utoken = $this->token()->utoken() ?? $this->token()->getCookieUToken();
        if ($utoken) {
            sql_delete(from: 'users_sessions')
                ->where(['token' => $this->token()->getTokenString($utoken)])
                ->delete();
        }
        $this->setDefaultStatus();
        return $this;
    }

    public function isAuthorized(): bool
    {
        $this->initialize();
        return $this->authorized;
    }
    
    /**
     * Verify if users' role is administrator (not lower or higher)
     */
    public function isAdmin(): bool
    {
        $this->initialize();
        $admin_level = null;
        foreach ($this->accessList() as $access) {
            if (!in_array('admin', $access['allow'])) {
                continue;
            } elseif (is_null($admin_level)) {
                $admin_level = $access['id'];
            } elseif ($access['id'] > $admin_level) {
                $admin_level = $access['id'];
            }
        }
        return $this->verifyAccessLevel($admin_level);
    }

    /**
     * Verify if users' role is webmaster (not lower or higher)
     */
    public function isMaster(): bool
    {
        return $this->verifyAccessLevel(self::ACCESS_LEVEL_MASTER);
    }

    /**
     * @param string|string[] $uid
     */
    public function getNameByUid(string|array $uid): ?array
    {
        $this->initialize();
        if (is_string($uid)) {
            $uid = preg_split('/\D+/', $uid);
        }
        $names = sql_select()
            ->from(['users'])
            ->columns(['uid', 'nickname'], 'users')
            ->where(['uid' => $uid], 'IN')
            ->all();
            
        if (empty($names)) {
            return null;
        }
        $result = [];
        foreach ($names as $i => $row) {
            $result[$row['uid']] = $row['nickname'] . '_#' . $row['uid'];
        }
        return $result;
    }

    public function name(): ?string
    {
        return session()->get(User::SESSUID . '/udata/nickname');
    }

    public function id(): int
    {
        return session()->get(User::SESSUID . '/udata/id');
    }

    public function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public function getOpenedSessions(): array
    {
        if (!$this->id()) {
            $this->opened_sessions = [];
        } else if (!isset($this->opened_sessions)) {
            $sql = sql_select(from: 'users_sessions');
            $sql->columns(['token', 'browser', 'platform', 'updated', 'ip']);
            $sql->where(['uid' => $this->id()]);
            $sql->useFunction('users_sessions.updated', 'UNIX_TIMESTAMP', 'updated');
            $this->opened_sessions = $sql->all();
            foreach ($this->opened_sessions as &$s) {
                $s['date'] = new DateFormat($s['updated'], DateFormat::FULL_COMPACT);
            }
        }
        return $this->opened_sessions;
    }
}
