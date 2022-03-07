<?php

namespace Blog\Request\Validators;

#[\Attribute]
class Required implements ValidatorInterface
{
    public function __construct(
        protected bool $required
    ) {
        
    }

    public function validate($value): ?string
    {
        if ($this->required && (empty($value) || !$value)) {
            return "Field `@field_name` is required.";
        }
        return null;
    }
}
