<?php

namespace Blog\Modules\User\Components;

use Blog\Request\LoginRequest;
use Blog\Modules\User\User;

trait UserAuthMethods
{
    public function authorize(LoginRequest $login_data): bool
    {
        $udata = sql_select(from: ['u' => 'users'])
            ->join(table: ['us' => 'users_statuses_list'], using: 'usid')
            ->join(table: ['uses' => 'users_sessions'], using: 'uid')
            ->columns([
                'u' => ['uid', 'mail', 'pwhash', 'nickname', 'registered', 'usid'],
                'us' => ['status', 'status_label' => 'label'],
                'uses' => ['agent_hash', 'browser', 'platform', 'updated']
            ])->where(['u.mail' => $login_data->mail])
            ->first();
        if (empty($udata)) {
            return false;
        } elseif (!password_verify($login_data->password, $udata['pwhash'])) {
            return false;
        }
        $user = [
            'data' => [
                'id' => $udata['uid'],
                'mail' => $udata['mail'],
                'nickname' => $udata['nickname'],
                'registered' => $udata['registered'],
            ],
            'status' => [
                'id' => $udata['usid'],
                'status' => $udata['status'],
                'label' => $udata['status_label']
            ]
        ];
        $this->setAuthorizedStatus($user, $login_data->remember_me);
        return true;
    }

    protected function setAuthorizedStatus(array $user, bool $remember): self
    {
        $utoken = $this->token()->generate();
        $token = $this->token()->getTokenString($utoken);
        session()->set(User::SESSUID . '/udata', $user['data']);
        session()->set(User::SESSUID . '/status', $user['status']);
        session()->set(User::SESSUID . '/token', $utoken);
        if ($remember) {
            $this->token()->setCookieUToken($utoken);
        }
        return $this->storeNewSession($token, $user['data']['id']);
    }

    protected function storeNewSession(string $token, int $uid): self
    {
        $this->removePreviousSession($uid, app()->user()->agent()->hash());
        sql_insert('users_sessions')
            ->set(
                [
                    $uid, $token,
                    app()->user()->agent()->hash(),
                    app()->user()->agent()->browser(),
                    app()->user()->agent()->platform(),
                    time()
                ],
                [
                    'uid', 'token', 'agent_hash',
                    'browser', 'platform', 'updated'
                ]
            )->exe();
        return $this;
    }

    protected function removePreviousSession(int $uid, string $agent_hash): self
    {
        $delete = sql_update(table: 'users_sessions')
            ->where(['uid' => $uid])
            ->andWhere(['agent_hash' => $agent_hash]);

        $delete->delete();
        return $this;
    }
}
