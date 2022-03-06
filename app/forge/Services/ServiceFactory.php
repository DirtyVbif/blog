<?php

namespace BlogForge\Services;

class ServiceFactory
{
    public static function callback(string $requested_action): void
    {
        $parts = preg_split('/\:+/', $requested_action);
        $service = "\\BlogForge\\Services\\" . pascalCase($parts[0]);
        $method = $parts[1] ?? null;
        if (!class_exists($service)) {
            $service = new UnknownService;
        } else {
            $service = new $service;
        }
        $service->run($method);
        return;
    }
}
