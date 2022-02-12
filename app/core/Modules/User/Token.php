<?php

namespace Blog\Modules\User;

class Token
{
    public const COOKIE_USER_TOKEN = 'user-remembered-token';
    protected int $lifetime;
    protected int $timeout;
    protected array $udata;
    protected array $pattern;

    public function __construct()
    {
        $this->prepareTokenPattern();
        $this->lifetime = (int)app()->config('user')->utoken_lifetime;
        $this->timeout = (int)app()->config('user')->utoken_timeout;
        return $this;
    }

    public function setCookieToken(string $token): self
    {
        if (preg_match('/[\w-]{32}/i', $token)) {
            $utoken = $this->setTokenTimestamp($token);
            $this->setCookieUToken($utoken);
        }
        return $this;
    }

    protected function prepareTokenPattern(): self
    {
        $l = strlen(time()) - 7;
        $of = (object)[
            'p1' => 7,
            't1' => 3,
            'p2' => 8,
            't2' => $l,
            'p3' => 10,
            't3' => 4,
            'p4' => 7
        ];
        $this->pattern = [
            'offsets' => $of,
            'token-pattern' => "/^([\w-]{{$of->p1}})"
                . "([\w-]{{$of->p2}})"
                . "([\w-]{{$of->p3}})"
                . "([\w-]{{$of->p4}})$/i",
            'utoken-pattern' => "/^([\w-]{{$of->p1}})"
                . "(\d{{$of->t1}})"
                . "([\w-]{{$of->p2}})"
                . "(\d{{$of->t2}})"
                . "([\w-]{{$of->p3}})"
                . "(\d{{$of->t3}})"
                . "([\w-]{{$of->p4}})$/i",
            'timestamp-mask' => '$2$4$6',
            'token-mask' => '$1$3$5$7'
        ];
        return $this;
    }

    public function generate(): string
    {
        do {
            $token = strRand(32, true);
        // } while (!empty(sql()->selectFirst("SELECT * FROM `users_sessions` WHERE `token` = '$token';")));
        } while (!empty(sql_select(from: 'users_sessions')->where(['token' => $token])->first()));
        $utoken = $this->setTokenTimestamp($token);
        return $utoken;
    }

    protected function setTokenTimestamp(string $token): string
    {
        if ($this->verifyUToken($token)) {
            return $this->updateUTokenTimestamp($token);
        }
        $time = $this->getTokenTimestampParts();
        $utoken = preg_replace($this->pattern['token-pattern'], '$1%s$2%s$3%s$4', $token);
        $utoken = sprintf($utoken, $time[1], $time[2], $time[3]);
        return $utoken;
    }

    protected function updateUTokenTimestamp(string $utoken): string
    {
        if (preg_match($this->pattern['token-pattern'], $utoken)) {
            return $this->setTokenTimestamp($utoken);
        }
        $time = $this->getTokenTimestampParts();
        $utoken = preg_replace($this->pattern['utoken-pattern'], '$1%s$3%s$5%s$7', $utoken);
        $utoken = sprintf($utoken, $time[1], $time[2], $time[3]);
        return $utoken;
    }

    protected function getTokenTimestampParts(): array
    {
        $time = (string)time();
        $parts = [];
        $parts[1] = substr($time, 0, $this->pattern['offsets']->t1);
        $parts[2] = substr($time, $this->pattern['offsets']->t1, $this->pattern['offsets']->t2);
        $parts[3] = substr($time, $this->pattern['offsets']->t1 + $this->pattern['offsets']->t2);
        return $parts;
    }

    /**
     * Verify and get current UserToken string from session container if it exists
     */
    public function utoken(): ?string
    {
        $utoken = session()->get(User::SESSUID . '/token');
        if (!$this->verifyUToken($utoken)) {
            return null;
        }
        return $utoken;
    }

    public function getTokenTimestamp(?string $utoken): int
    {
        if (!$this->verifyUToken($utoken)) {
            return 0;
        }
        $timestamp = preg_replace($this->pattern['utoken-pattern'], $this->pattern['timestamp-mask'], $utoken);
        return (int)$timestamp;
    }

    public function getTokenString(string $utoken): ?string
    {
        if (!$this->verifyUToken($utoken)) {
            return null;
        }
        return preg_replace($this->pattern['utoken-pattern'], $this->pattern['token-mask'], $utoken);
    }

    public function verifyTokenLifetime(?string $utoken): bool
    {
        if (!$this->verifyUToken($utoken)) {
            return false;
        }
        $time = $this->getTokenTimestamp($utoken);
        return time() - $time < $this->lifetime;
    }

    public function verifySessionTokenTimeout(): bool
    {
        $utoken = $this->utoken();
        return $this->verifyUTokenTimeout($utoken);
    }

    public function verifyUTokenTimeout(?string $utoken): bool
    {
        $time = $this->getTokenTimestamp($utoken);
        $timeout = time() - $time;
        if ($timeout < $this->timeout) {
            $seconds = $this->timeout - $timeout;
            return true;
        }
        return false;
    }

    public function verify(): bool
    {
        $utoken = $this->utoken();
        $remembered_utoken = $this->getCookieUToken();
        if (!$utoken && !$remembered_utoken) {
            return false;
        } elseif (!$this->verifyTokenLifetime($utoken) && !$this->verifyTokenLifetime($remembered_utoken)) {
            return false;
        }
        return $this->isSessionExists($utoken, $remembered_utoken);
    }

    public function verifyUToken(?string $utoken): bool
    {
        if (!$utoken) {
            return false;
        }
        return preg_match($this->pattern['utoken-pattern'], $utoken);
    }

    protected function isSessionExists(?string $utoken, ?string $remembered_utoken)
    {
        $query = sql_select(from: ['uses' => 'users_sessions'])
            ->join(table: ['u' => 'users'], using: 'uid')
            ->join(table: ['us' => 'users_statuses_list'], using: 'usid')
            ->columns([
                'u' => ['uid', 'mail', 'nickname', 'registered'],
                'us' => ['usid', 'status', 'status_label' => 'label'],
                'uses' => ['browser', 'platform', 'updated']
            ]);
        if ($utoken) {
            $token = $this->getTokenString($utoken);
            $this->udata = $query->where(['uses.token' => $token])->first();
        } elseif ($remembered_utoken) {
            $token = $this->getTokenString($remembered_utoken);
            $this->udata = $query->where(['uses.token' => $token])
                ->andWhere(['uses.agent_hash' => app()->user()->agent()->hash()])
                ->first();
        }
        if (!isset($this->udata['uid'])) {
            return false;
        }
        return $this->updateUserSession();
    }

    protected function updateUserSession(): bool
    {
        $utoken = $this->utoken();
        $remembered_utoken = $this->getCookieUToken();
        if (!$utoken && $remembered_utoken) {
            $utoken = $remembered_utoken;
        } elseif (!$utoken && !$remembered_utoken) {
            return false;
        }
        $utoken_new = $this->updateUTokenTimestamp($utoken);
        $token = $this->getTokenString($utoken);
        $time = $this->getTokenTimestamp($utoken_new);
        $update_result = sql_update(table: 'users_sessions')
            ->set([
                'agent_hash' => app()->user()->agent()->hash(),
                'browser' => app()->user()->agent()->browser(),
                'platform' => app()->user()->agent()->platform(),
                'updated' => $time
            ])->where(['uid' => $this->udata['uid']])
            ->andWhere(['token' => $token]);
        if (!$update_result->update()) {
            msgr()->debug([
                'message' => 'Following SQL-request made zero changes',
                'update-sql' => $update_result->raw(),
                'update-sql-data' => $update_result->data(),
                'utoken' => $this->utoken(),
                'token' => $token,
                'utoken_new' => $utoken_new,
                'timestamp-new-token' => $time
            ]);
            return false;
        }
        session()->set(User::SESSUID . '/status', [
            'id' => $this->udata['usid'],
            'status' => $this->udata['status'],
            'label' => $this->udata['status_label']
        ]);
        session()->set(User::SESSUID . '/udata', [
            'id' => $this->udata['uid'],
            'mail' => $this->udata['mail'],
            'nickname' => $this->udata['nickname'],
            'registered' => $this->udata['registered']
        ]);
        session()->set(User::SESSUID . '/token', $utoken_new);
        $this->setCookieUToken($utoken_new);
        return true;
    }

    public function getUserStatus(): array
    {
        if (!isset($this->user_status['id'], $this->user_status['status'], $this->user_status['label'])) {
            return [];
        }
        return $this->user_status;
    }

    public function getCookieUToken(): ?string
    {
        $utoken = cookies()->get(self::COOKIE_USER_TOKEN);
        if (!$utoken) {
            return null;
        }
        $utoken = preg_replace('/(^.{5})(.*)(.{5}$)/', '$2', $utoken);
        if (!$this->verifyUToken($utoken)) {
            cookies()->unset(self::COOKIE_USER_TOKEN);
            return null;
        }
        return $utoken;
    }

    public function setCookieUToken(string $utoken): self
    {
        if ($this->verifyUToken($utoken)) {
            $cookie_token = strRand(5, true) . $utoken . strRand(5, true);
            cookies()->set(self::COOKIE_USER_TOKEN, $cookie_token, time() + $this->lifetime);
        }
        return $this;
    }
}
