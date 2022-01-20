<?php

namespace Blog\Components;

trait SetterAndGetter
{
    /**
     * Parse requested string for session storage into array with key names
     */
    protected function parseKey(string $key): array
    {
        $key = preg_replace('/[\.\/\\\]+/', '/', $key);
        $key = explode('/', $key);
        return $key;
    }

    /**
     * Store value into session container by name or names sequence
     * 
     * @param string $key name or names sequence of value in session container.
     * Names sequence separeted with `/` or `.` or `\` symbols.
     * * e.g.: `$key = 'user/token'` will store `$value` into `$_SESSION['user']['token']` container
     * 
     * @param mixed $value
     * 
     * @param bool $rewrite trigger to rewrite by given name existing value or not
     */
    protected function setByVarName(string $var_name, $value, bool $use_this = true, ?string $name = null, bool $rewrite = true): self
    {
        if (is_null($name)) {
            if (
                $use_this
                && (
                    $rewrite
                    || (!$rewrite && !isset($this->$var_name))
                )
            ) {
                $this->$var_name = $value;
            } else if (
                !$use_this
                && (
                    $rewrite
                    || (!isset($GLOBALS[$var_name]) && !$rewrite)
                )
            ) {
                $GLOBALS[$var_name] = $value;
            }
        } else {
            $k = $this->parseKey($name);
            $key = array_shift($k);
            if ($use_this) {
                if (!is_array($this->$var_name) && !$rewrite) {
                    return $this;
                } else if (!is_array($this->$var_name)) {
                    $this->$var_name = [0 => $this->$var_name];
                }
                $this->$var_name[$key] = setamdv($k, $this->$var_name[$key] ?? null, $value, $rewrite);
            } else {
                if (!is_array($GLOBALS[$var_name]) && !$rewrite) {
                    return $this;
                } else if (!is_array($GLOBALS[$var_name])) {
                    $GLOBALS[$var_name] = [0 => $GLOBALS[$var_name]];
                }
                $GLOBALS[$var_name][$key] = setamdv($k, $GLOBALS[$var_name][$key] ?? null, $value, $rewrite);
            }
        }
        return $this;
    }

    /**
     * Get value from session container by name or names sequence.
     * 
     * @param string|null $key [optional] name or names sequence of array keys for session container.
     * Names sequence can be separeted with `/` or `.` or `\` symbols.
     * * e.g.: `$key = 'user/token'` will return `$_SESSION['user']['token']` value if it exists;
     * * full session container will be returned if no key provided;
     */
    protected function getByVarName(string $var_name, bool $use_this = true, ?string $key = null)
    {
        if ($use_this) {
            $result = $this->$var_name ?? null;
        } else {
            $result = $GLOBALS[$var_name] ?? null;
        }
        if (!is_null($key) && !is_null($result)) {
            $k = $this->parseKey($key);
            $i = array_shift($k);
            $result = $result[$i] ?? null;
            if (is_array($result)) {
                for ($i = 0; $i < count($k); $i++) {
                    if (!is_array($result)) {
                        break;
                    }
                    $result = $result[$k[$i]] ?? null;
                }
            }
        }
        return $result;
    }
}