<?php

namespace BlogForge\Services;

class ServicePrototype
{
    protected \BlogForge\Forge $forge;

    public function __construct()
    {
        $this->forge = \BlogForge\Forge::instance();
    }

    protected function normalizeClassname(string $classname): array
    {
        $classname = preg_replace('/[\.\/\\\]+/', '\\', $classname);
        $classname = preg_replace('/^(\\\?blog)?(\\\)?(.*)/i', '$3', $classname);
        $parts = explode('\\', $classname);
        foreach ($parts as $i => $part) {
            $parts[$i] = pascalCase($part);
        }
        $classname = array_pop($parts);
        return [
            'class' => $classname,
            'namespace' => $parts
        ];
    }
}
