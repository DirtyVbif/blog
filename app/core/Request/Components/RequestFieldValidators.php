<?php

namespace Blog\Request\Components;

trait RequestFieldValidators
{
    // ------------------------------------------------------------------------------>
    // VALIDATORS BY FIELD TYPE

    /**
     * Validate field of type `string`
     */
    protected function validateFieldString(string $field_name, array $rules): array
    {
        $errors = [];
        $value = $this->data[$field_name] ?? null;
        foreach ($rules as $rule_name => $valid) {
            switch ($rule_name) {
                case 'required':
                    $this->validateRequiredValue($value, $valid, $field_name, $errors);
                    break;
                case 'max_length':
                    $this->validateStringMaxLength($value, $valid, $field_name, $errors);
                    break;
                case 'pattern':
                    $this->validateStringPattern($value, $valid, $field_name, $errors);
                    break;
            }
        }
        return $errors;
    }

    /**
     * Validate field of type `plain_text`
     */
    protected function validateFieldPlainText(string $field_name, array $rules): array
    {
        $value = $this->data[$field_name] ?? null;
        $errors = [];
        foreach ($rules as $rule_name => $valid) {
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

    /**
     * Validate field of type `boolean`
     */
    protected function validateFieldBoolean(string $field_name, array $rules): array
    {
        $value = ($this->data[$field_name] ?? false) ? true : false;
        $errors = [];
        foreach ($rules as $rule_name => $valid) {
            switch ($rule_name) {
                case 'required':
                    $this->validateRequiredValue($value, $valid, $field_name, $errors);
                    break;
            }
        }
        $this->data[$field_name] = $value;
        return $errors;
    }

    /**
     * Validate field of type `int`
     */
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

    /**
     * Validate field of type `plain_text`
     */
    protected function validateFieldHtmlText(string $field_name, array $rules): array
    {
        $value = $this->data[$field_name] ?? null;
        $errors = [];
        foreach ($rules as $rule_name => $valid) {
            switch ($rule_name) {
                case 'required':
                    $this->validateRequiredValue($value, $valid, $field_name, $errors);
                    break;
            }
        }
        if (isset($this->data[$field_name])) {
            $this->data[$field_name] = htmlspecialchars($this->data[$field_name]);
        }
        return $errors;
    }

    
    // ------------------------------------------------------------------------------>
    // FIELD VALUE VALIDATORS BY RULE

    /**
     * Validate field rule `reqiered`
     */
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

    /**
     * Validate field rule `max_length`
     */
    protected function validateStringMaxLength(string $value, int $max, string $field_name, array &$errors): bool
    {
        if (mb_strlen($value) > $max) {
            $errors[] = t(
                'Field `@field_name` length must be lesser than @n symbols.',
                [
                    'field_name' => $this->getFieldName($field_name),
                    'n' => $max
                ]
            );
            return false;
        }
        return true;
    }

    /**
     * Validate field rule `pattern`
     */
    protected function validateStringPattern(string $value, string $pattern, string $field_name, array &$errors): bool
    {
        if (!preg_match($pattern, $value)) {
            $errors[] = t(
                'Field `@field_name` contains invalid value.',
                ['field_name' => $this->getFieldName($field_name)]
            );
            return false;
        }
        return true;
    }
}
