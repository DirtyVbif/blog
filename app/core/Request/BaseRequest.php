<?php

namespace Blog\Request;

abstract class BaseRequest
{
    protected bool $is_valid;
    protected array $errors = [];

    public function __construct(
        protected array $data
    ) {
        $this->validate();
        $this->outputErrors();
    }

    abstract protected function rules(): array;

    abstract protected function getFieldName(string $name): string;

    public function isValid(): bool
    {
        return $this->is_valid ?? false;
    }

    protected function validate(): void
    {
        $this->is_valid = true;
        foreach ($this->rules() as $name => $rule) {
            if (!isset($this->data[$name])) {
                $this->is_valid = false;
                $this->errors[$name] = [
                    t(
                        'Field `@field_name` is required.',
                        ['field_name' => $this->getFieldName($name)]
                    )
                ];
            } else {
                $this->errors[$name] = $this->validateField($name, $rule);
                if (!empty($this->errors[$name])) {
                    $this->is_valid = false;
                }
            }
        }
    }

    /**
     * @return array with errors if validation failed or empty array without errors if validation passed
     */
    protected function validateField(string $field_name, array $rule)
    {
        $validator = 'validateField' . strPascalCase($rule['type']);
        unset($rule['type']);
        return $this->$validator($field_name, $rule);
    }

    protected function outputErrors(): void
    {
        foreach ($this->errors as $field_errors) {
            foreach ($field_errors as $error) {
                msgr()->error($error);
            }
        }
        return;
    }
}
