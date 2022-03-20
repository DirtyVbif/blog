<?php

namespace Blog\Request\Validators;

#[\Attribute]
class Pattern implements ValidatorInterface
{
    public function __construct(
        protected string $pattern
    ) {
        
    }

    /**
     * @param string $value
     */
    public function validate($value): ?String
    {
        if (!settype($value, 'string')) {
            return "Field `@field_name` has wrong value type. It must be of type `string`.";
        } else if (!preg_match($this->pattern, $value)) {
            return "Field `@field_name` contains invalid value or symbols.";
        }
        return null;
    }
}
