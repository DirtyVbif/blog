<?php

namespace Blog\Modules\CSRF;

class Token
{
    protected const SESSID = 'csrf-token';
    protected const TOKEN_PATTERN = '/^(\w{32})(\:)(\d+)$/';
    public const FORM_ID = 'csrf-token';

    protected int $lifetime;

    public function __construct()
    {
        $this->lifetime = app()->config('user')->csrf_token_lifetime;
        $this->initialize();
    }

    public function __toString()
    {
        return $this->get();
    }

    protected function initialize(): void
    {
        $session_token = session()->get(self::SESSID);
        if (
            ($session_token && !$this->validateLifetime($session_token))
            || !$session_token
        ) {
            $this->regenerate();
        }
        return;
    }

    protected function validateLifetime(string $session_token): bool
    {
        $token_lifetime = (int)preg_replace(self::TOKEN_PATTERN, '$3', $session_token);
        $time = (int)time();
        return ($time - $token_lifetime) < $this->lifetime;
    }

    protected function regenerate(): void
    {
        session()->set(self::SESSID, $this->generate());
        return;
    }

    protected function generate(): string
    {
        return md5(strRand(32)) . ':' . time();
    }

    public function validate(string $token, bool $regenerate = true): bool
    {
        $validation = $this->get() === $token;
        if ($regenerate && $validation) {
            $this->regenerate();
        }
        return $validation;
    }

    public function get(bool $full_token = false): string
    {
        $session_token = session()->get(self::SESSID);
        return $full_token ? $session_token : preg_replace(self::TOKEN_PATTERN, '$1', $session_token);
    }

    public function render(): string
    {
        $output = '<input type="hidden" name="%s" value="%s">';
        return sprintf($output, self::FORM_ID, $this->get());
    }
}
