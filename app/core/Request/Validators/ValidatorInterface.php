<?php

namespace Blog\Request\Validators;

interface ValidatorInterface
{
    /**
     * Validate value and return null if valid or error text if invalid.
     * 
     * @return string with error text if validation failed
     * @return null if value is valid
     */
    public function validate($value): ?string;
}
