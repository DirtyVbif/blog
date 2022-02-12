<?php

namespace Blog\Modules\User\Components;

use Blog\Modules\User\UserAgent;
use Blog\Modules\User\Token;
use Blog\Modules\User\User;

trait UserGetSetMethods
{
    protected Token $token;
    protected UserAgent $agent;
    protected ?string $utoken;

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

    public function name(): ?string
    {
        return $_SESSION[User::SESSUID]['udata']['nickname'] ?? null;
    }

    public function uid(): int
    {
        return $_SESSION[User::SESSUID]['udata']['id'] ?? 0;
    }
}
