<?php

namespace Blog\Request;

use Blog\Modules\CSRF\Token;

abstract class BaseRequest
{
    use Components\BaseRequestFieldValidators;

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
        if (!$this->validateCsrfToken()) {
            return;
        }
        $this->is_valid = true;
        foreach ($this->rules() as $name => $rule) {
            if (!isset($this->data[$name]) && ($rule['required'] ?? false)) {
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

    protected function validateCsrfToken(): bool
    {
        $csrf_token = $this->data[Token::FORM_ID] ?? null;
        if (!$csrf_token || !app()->csrf()->validate($csrf_token)) {
            $this->is_valid = false;
            $this->errors[Token::FORM_ID] = [t('Form token is invalid or timed out. Please try again or contact administrator.')];
            return false;
        }
        unset($this->data[Token::FORM_ID]);
        return true;
    }

    /**
     * @return array with errors if validation failed or empty array without errors if validation passed
     */
    protected function validateField(string $field_name, array $rule)
    {
        $validator = 'validateField' . pascalCase($rule['type']);
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
