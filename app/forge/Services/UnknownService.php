<?php

namespace BlogForge\Services;

class UnknownService extends ServicePrototype implements ServiceInterface
{
    public function run(?string $action = null): void
    {
        $this->default();
        return;
    }

    public function default(): void
    {
        $this->forge->setError("There is no services for requested action.");
        return;
    }
}
