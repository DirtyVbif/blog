<?php

namespace Blog\Modules\User\Components;

use Blog\Request\LoginRequest;
use Blog\Modules\User\User;

trait UserAuthMethods
{
    public function authorize(LoginRequest $login_data): bool
    {
        $udata = sql_select(from: ['u' => 'users'])
            ->join(table: ['us' => User::TBL_STATUSES], using: 'usid')
            ->join(table: ['uses' => 'users_sessions'], using: 'uid')
            ->columns([
                'u' => ['uid', 'mail', 'pwhash', 'nickname', 'registered', 'usid'],
                'us' => ['status', 'status_label' => 'label'],
                'uses' => ['token', 'agent_hash', 'browser', 'platform', 'updated', 'ip']
            ])->where(['u.mail' => $login_data->mail])
            ->all();
        if (empty($udata)) {
            return false;
        } elseif (!password_verify($login_data->password, $udata[0]['pwhash'])) {
            return false;
        }
        $user = [
            'data' => [
                'id' => $udata[0]['uid'],
                'mail' => $udata[0]['mail'],
                'nickname' => $udata[0]['nickname'],
                'registered' => $udata[0]['registered'],
            ],
            'status' => [
                'id' => $udata[0]['usid'],
                'status' => $udata[0]['status'],
                'label' => $udata[0]['status_label']
            ]
        ];
        $this->setAuthorizedStatus($user, $login_data->remember_me);
        $this->checkTimeoutSession($udata);
        return true;
    }

    protected function setAuthorizedStatus(array $user, bool $remember): void
    {
        $utoken = user()->token()->generate();
        $token = user()->token()->getTokenString($utoken);
        session()->set(User::SESSUID . '/udata', $user['data']);
        session()->set(User::SESSUID . '/status', $user['status']);
        session()->set(User::SESSUID . '/token', $utoken);
        if ($remember) {
            user()->token()->setCookieUToken($utoken);
        }
        $this->storeNewSession($token, $user['data']['id']);
        return;
    }

    protected function storeNewSession(string $token, int $uid): void
    {
        $this->removePreviousSession($uid, user()->agent()->hash());
        sql_insert('users_sessions')
            ->set(
                [
                    $uid, $token,
                    user()->agent()->hash(),
                    user()->agent()->browser(),
                    user()->agent()->platform(),
                    time(), user()->ip()
                ],
                [
                    'uid', 'token', 'agent_hash',
                    'browser', 'platform', 'updated', 'ip'
                ]
            )->exe();
        return;
    }

    protected function removePreviousSession(int $uid, string $agent_hash): void
    {
        $delete = sql_update(table: 'users_sessions')
            ->where(['uid' => $uid])
            ->andWhere(['agent_hash' => $agent_hash]);

        $delete->delete();
        return;
    }

    protected function checkTimeoutSession(array $udata): void
    {
        foreach ($udata as $row) {
            if (
                !$row['token']
                || (time() - $row['updated']) < app()->config('user')->utoken_lifetime
            ) {
                continue;
            }
            $delete = sql_update(table: 'users_sessions');
            $delete->where(['token' => $row['token']]);
            $delete->delete();
        }
        return;
    }
}
