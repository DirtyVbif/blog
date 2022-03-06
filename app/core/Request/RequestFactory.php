<?php

namespace Blog\Request;

use Exception;

class RequestFactory
{
    public static function get(string $name): RequestPrototype
    {
        $class = "\\Blog\\Request\\" . pascalCase("{$name} request");
        if (!class_exists($class)) {
            pre("Failed to build {$class}::object.");
            die;
        } else if (!(new $class instanceof RequestPrototype)) {
            pre("{$class}::class doesn't instance of \\Blog\\Request\\RequestPrototype");
            die;
        }
        return new $class;
    }
}
