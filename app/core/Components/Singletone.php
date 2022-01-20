<?php

namespace Blog\Components;

trait Singletone
{
    protected static self $_instance;
    
    final public static function instance(): self
    {
        if(!isset(self::$_instance)) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    final protected function __construct() {}

    final protected function __clone() {}

    final public function __wakeup()
    {
        throw new \Exception("Cannot serialize a ".__CLASS__."::object.");
    }
}
