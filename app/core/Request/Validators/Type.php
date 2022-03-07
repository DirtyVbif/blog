<?php

namespace Blog\Request\Validators;

use JetBrains\PhpStorm\ExpectedValues;

#[\Attribute]
class Type implements ValidatorInterface
{
    public function __construct(
        #[ExpectedValues(["bool", "boolean", "int", "integer", "float", "double", "string", "array", "object", "null"])]
        protected string $type
    ) {
        
    }

    public function validate($value): ?string
    {
        if (!settype($value, $this->type)) {
            return "Field `@field_name` has wrong value type. It must be of type `{$this->type}`.";
        }
        return null;
    }
}
