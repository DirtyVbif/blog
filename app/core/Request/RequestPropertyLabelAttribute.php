<?php

namespace Blog\Request;

#[\Attribute]
class RequestPropertyLabelAttribute
{
    public function __construct(
        protected string $label
    ) {
        
    }

    public function get(): string
    {
        return $this->label;
    }
}
