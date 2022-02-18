<?php

namespace Blog\Components;

abstract class AbstractModule
{
    public function getPath(): string
    {
        $class_name = array_pop(preg_split('/\/|\\\/', get_class($this)));
        return COREDIR . "Modules/{$class_name}/";
    }
}