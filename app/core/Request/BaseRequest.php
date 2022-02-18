<?php

namespace Blog\Request;

use Blog\Modules\CSRF\Token;

abstract class BaseRequest
{
    use Components\BaseRequestFieldValidators;

    protected const ACCESS_LEVEL = 1;
    public const SESSID = 'last-request-data';

    protected bool $is_valid;
    protected array $errors = [];

    public function __construct(
        protected array $data
    ) {
        $this->validate();
        $this->outputErrors();
        $this->flushSession();
    }

    abstract protected function rules(): array;

    abstract protected function getFieldName(string $name): string;

    /**
     * Checks if request data is valid
     */
    public function isValid(): bool
    {
        return $this->is_valid ?? false;
    }

    protected function validate(): void
    {
        $this->rememberValues();
        if (!app()->user()->verifyAccessLevel(static::ACCESS_LEVEL)) {
            msgr()->error(t('You have no permission for that action. If you think that it\'s an error, please contact administrator.'));
            return;
        }
        $rules = $this->rules();
        $csrf_skip = false;
        if (isset($rules['csrf-token'])) {
            $csrf_skip = $rules['csrf-token']['skip'] ?? false;
            unset($rules['csrf-token']);
        }
        // validate form CSRF token
        if (!$this->validateCsrfToken($csrf_skip)) {
            return;
        }
        // authorize valid form fields
        foreach ($this->data as $field_name => $value) {
            if (!$this->validateFieldName($field_name)) {
                unset($this->data[$field_name]);
            }
        }
        $this->is_valid = true;
        // validate form fields value
        foreach ($rules as $name => $rule) {
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
        return;
    }

    protected function rememberValues(): void
    {
        foreach ($this->data as $key => $value) {
            session()->set(
                self::SESSID . '/' . $key,
                $value
            );
        }
        return;
    }

    protected function flushSession(): void
    {
        if ($this->isValid()) {
            session()->unset(self::SESSID);
        }
        return;
    }

    protected function validateCsrfToken(bool $skip): bool
    {
        if ($skip) {
            return true;
        }
        $csrf_token = $this->data[Token::FORM_ID] ?? null;
        if (!$csrf_token || !app()->csrf()->validate($csrf_token)) {
            $this->is_valid = false;
            $this->errors[Token::FORM_ID] = [t('Form token is invalid or timed out. Please try again or contact administrator.')];
            return false;
        }
        unset($this->data[Token::FORM_ID]);
        return true;
    }

    protected function validateFieldName(string $field_name): bool
    {
        $rules = $this->rules();
        return isset($rules[$field_name]);
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

    /**
     * Set default values on emtpy fields
     * 
     * @param array $defaults with `field_name => value` pairs of default values for fields by field name as array key.
     */
    public function setDefaultValues(array $defaults): void
    {
        foreach ($defaults as $field_name => $value) {
            if ($this->data[$field_name] ?? false) {
                continue;
            }
            $this->data[$field_name] = $value;
        }
        return;
    }

    public function set(string $field_name, $value): void
    {
        $this->data[$field_name] = $value;
        return;
    }

    public function get(string $field_name)
    {
        return $this->data[$field_name] ?? null;
    }
}
