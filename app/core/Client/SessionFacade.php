<?php

namespace Blog\Client;

use Blog\Components\SetterAndGetter;
use Blog\Components\Singletone;

class SessionFacade
{
    use Singletone,
        SetterAndGetter;

    /**
     * Start new session or initialize existing one
     */
    public function start(): self
    {
        session_start();
        return $this;
    }

    /**
     * Store value into session container by name or names sequence
     * 
     * @param string $name name or names sequence of value in session container.
     * Names sequence separeted with `/` or `.` or `\` symbols.
     * * e.g.: `$name = 'user/token'` will store `$value` into `$_SESSION['user']['token']` container
     * 
     * @param mixed $value
     * 
     * @param bool $rewrite trigger to rewrite by given name existing value or not
     */
    public function set(string $name, $value, bool $rewrite = true): self
    {
        return $this->setByVarName('_SESSION', $value, false, $name, $rewrite);
    }

    public function push(string $name, $value): self
    {
        if (!$this->get($name) || !is_array($this->get($name))) {
            $this->set($name, []);
        }
        $array = $this->get($name);
        array_push($array, $value);
        $this->set($name, $array);
        return $this;
    }

    /**
     * Get value from session container by name or names sequence.
     * 
     * @param string|null $name [optional] name or names sequence of array keys for session container.
     * Names sequence can be separeted with `/` or `.` or `\` symbols.
     * * e.g.: `$name = 'user/token'` will return `$_SESSION['user']['token']` value if it exists;
     * * full session container will be returned if no key provided;
     */
    public function get(?string $name = null)
    {
        return $this->getByVarName('_SESSION', false, $name);
    }

    /**
     * Check session container array if specified key is set or not
     * 
     * @param string $name name or names sequence of array keys for session container.
     * Names sequence can be separeted with `/` or `.` or `\` symbols.
     * * e.g.: `$name = 'user/token'` will return `$_SESSION['user']['token']` value if it exists;
     */
    public function isset(string $name): bool
    {
        return !is_null($this->get($name));
    }

    /**
     * Unset session container value by name or name sequence.
     * 
     * @param string $name name or names sequence of value in session container.
     * Names sequence separeted with `/` or `.` or `\` symbols.
     * * e.g.: `$name = 'user/token'` will return `$_SESSION['user']['token']` value if it exists;
     */
    public function unset(string $name): self
    {
        $k = $this->parseKey($name);
        $name = array_shift($k);
        if (empty($k) && isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        } elseif (isset($_SESSION[$name])) {
            $_SESSION[$name] = setamdv($k, $_SESSION[$name], unset: true);
        }
        return $this;
    }
}
