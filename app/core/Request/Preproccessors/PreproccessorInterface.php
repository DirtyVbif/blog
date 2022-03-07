<?php

namespace Blog\Request\Preproccessors;

use Blog\Request\RequestPrototype;

interface PreproccessorInterface
{
    public function format(string $field_name, RequestPrototype $request): void;
}
