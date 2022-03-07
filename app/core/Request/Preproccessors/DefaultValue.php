<?php

namespace Blog\Request\Preproccessors;

use Blog\Request\RequestPrototype;

#[\Attribute]
class DefaultValue implements PreproccessorInterface
{
    public function __construct(
        protected mixed $default_value
    ) {
        
    }

    public function format(string $field_name, RequestPrototype $request): void
    {
        if (!$request->raw($field_name)) {
            $request->set($field_name, $this->default_value);
        }
        return;
    }
}
