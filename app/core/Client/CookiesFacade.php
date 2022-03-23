<?php

namespace Blog\Client;

use Blog\Components\AjaxModule;
use Blog\Components\Singletone;
use Blog\Mediators\AjaxResponse;

class CookiesFacade implements AjaxModule
{
    use Singletone;

    public const COOKIESACCEPTED = 'cookies-accepted';

    /**
     * Get cookie by name
     */
    public function get(string $name)
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Send a cookie. Same as setcookie() function.
     * @see setcookie() documentation at @link https://php.net/manual/en/function.setcookie.php
     */
    public function set(string $name, $value, $expires_or_options = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
    {        
        setcookie($name, $value, $expires_or_options, $path, $domain, $secure, $httponly);
        return $this;
    }

    /**
     * Unset cookie by name
     */
    public function unset(string $name): self
    {
        unset($_COOKIE[$name]);
        setcookie($name, '', -1, '/');
        return $this;
    }

    /**
     * Check if specified cookie name exists or not
     */
    public function isset(string $name): bool
    {
        return !is_null($this->get($name));
    }

    public function setCookiesAccepted(): self
    {
        session()->set(self::COOKIESACCEPTED, true);
        return $this;
    }

    public function isCookiesAccepted(): bool
    {
        return session()->get(self::COOKIESACCEPTED) ?? false;
    }

    public function ajaxRequest(): AjaxResponse
    {
        $response = new AjaxResponse();
        if (($_POST['cookie-agreement'] ?? false) == 1) {
            $this->setCookiesAccepted();
            $response->setResponse('cookies accepted successfully');
        } else {
            $response->setCode(400);
            $response->setResponse('Error 400. Bad request.');
        }
        return $response;
    }
}
