<?php

namespace Blog\Request\Components;

trait BaseRequestFieldValidators
{
    protected function validateFieldString(string $field_name, array $rule): array
    {
        $errors = [];
        $value = $this->data[$field_name] ?? null;
        foreach ($rule as $name => $valid) {
            switch ($name) {
                case 'required':
                    if (!$this->validateRequiredValue($value, $valid)) {
                        $errors[] = t(
                            'Field `@field_name` is required.',
                            ['field_name' => $this->getFieldName($field_name)]
                        );
                    }
                    break;
                case 'max_length':
                    if (!$this->validateStringMaxLength($value, $valid)) {
                        $errors[] = t(
                            'Field `@field_name` length must be lesser than @n symbols.',
                            [
                                'field_name' => $this->getFieldName($field_name),
                                'n' => $valid
                            ]
                        );
                    }
                    break;
                case 'pattern':
                    if (!$this->validateStringPattern($value, $valid)) {
                        $errors[] = t(
                            'Field `@field_name` contains invalid value.',
                            ['field_name' => $this->getFieldName($field_name)]
                        );
                    }
                    break;
            }
        }
        return $errors;
    }

    protected function validateRequiredValue($value, bool $required): bool
    {
        if ($required && !$value) {
            return false;
        }
        return true;
    }

    protected function validateStringMaxLength(string $value, int $max): bool
    {
        return mb_strlen($value) <= $max;
    }

    protected function validateStringPattern(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value);
    }

    protected function validateFieldPlainText(string $field_name, array $rule)
    {
        
    }
}
