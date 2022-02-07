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
                    $this->validateRequiredValue($value, $valid, $field_name, $errors);
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

    protected function validateRequiredValue($value, bool $required, string $field_name, array &$errors): bool
    {
        if ($required && is_null($value)) {
            array_push(
                $errors,
                t(
                    'Field `@field_name` is required.',
                    ['field_name' => $this->getFieldName($field_name)]
                )
            );
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

    protected function validateFieldPlainText(string $field_name, array $rule): array
    {
        $value = $this->data[$field_name] ?? null;
        $errors = [];
        foreach ($rule as $rule_name => $valid) {
            switch ($rule_name) {
                case 'required':
                    $this->validateRequiredValue($value, $valid, $field_name, $errors);
                    break;
            }
        }
        if (isset($this->data[$field_name])) {
            $this->data[$field_name] = htmlspecialchars(strip_tags($this->data[$field_name]));
        }
        return $errors;
    }

    protected function validateFieldBoolean(string $field_name, array $rule): array
    {
        $value = ($this->data[$field_name] ?? false) ? true : false;
        $errors = [];
        foreach ($rule as $rule_name => $valid) {
            switch ($rule_name) {
                case 'required':
                    $this->validateRequiredValue($value, $valid, $field_name, $errors);
                    break;
            }
        }
        $this->data[$field_name] = $value;
        return $errors;
    }

    protected function validateFieldInt(string $field_name, array $rules): array
    {
        $value = $this->data[$field_name] ?? null;
        $errors = [];
        if ($value && !is_numeric($value)) {
            $errors[] = t(
                'Invalid value for field `@field_name`.',
                ['field_name' => $this->getFieldName($field_name)]
            );
        } else {
            foreach ($rules as $rule_name => $valid) {
                switch ($rule_name) {
                    case 'required':
                        $this->validateRequiredValue($value, $valid, $field_name, $errors);
                        break;
                }
            }
        }
        return $errors;
    }
}
