<?php

namespace Blog\Components;

trait Singletone
{
    private static self $_instance;

    public function __construct()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = $this;
        }

        return self::$_instance;
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a ".__CLASS__."::object.");
    }

    public function __clone()
    {
        
    }
    
    public static function instance(): self
    {
        if(!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
