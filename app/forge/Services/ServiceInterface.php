<?php

namespace BlogForge\Services;

interface ServiceInterface
{
    public function run(?string $method);
    public function default();
}
