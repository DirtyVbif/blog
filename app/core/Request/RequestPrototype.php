<?php

namespace Blog\Request;

use Blog\Modules\CSRF\Token;

/**
 * Each object remembers all provided values into session container and values must be cleared manualy.
 * 
 * If request wouldn't be valid then form keeps filled values from session container.
 * To clear remembered values must be called @method complete() manualy.
 */
abstract class RequestPrototype
{
    use Components\RequestFieldValidators;

    protected const ACCESS_LEVEL = 2;
    public const SESSID = 'last-request-data';
    protected const VALID = true;
    protected const SKIP_CSRF = false;

    protected bool $is_valid;
    protected array $errors = [];
    protected array $data;
    protected bool $validated;
    
    public function __get(string $name)
    {
        if (isset($this->data[$name]) && $this->isValid()) {
            return $this->data[$name];
        }
    }

    abstract protected function rules(): array;

    /**
     * Checks if request data is valid
     */
    public function isValid(): bool
    {
        if (!$this->validated()) {
            $this->validate();
        }
        return $this->is_valid ?? false;
    }

    protected function validated(): bool
    {
        return $this->validated ?? false;
    }

    /**
     * Validation of request data to correspondes with specified rules for request.
     * 
     * After validation you must use @method complete() on success to clear remembered values from session container.
     * Reason is that provided values storing into session containers to let users not to fill form again on error.
     * 
     * @param array $data values that must be validated. If $data array not provided manualy then validator tries to get $_POST data.
     */
    public function validate(array $data = []): self
    {
        if ($this->validated()) {
            return $this;
        }
        $this->validated = true;
        $this->data = empty($data) ? $_POST : $data;
        $this->rememberValues();
        if (!static::VALID) {
            return $this;
        } else if (!app()->user()->verifyAccessLevel(static::ACCESS_LEVEL)) {
            msgr()->error(t('You have no permission for that action. If you think that it\'s an error, please contact administrator.'));
            return $this;
        }
        // validate form CSRF token
        if (!$this->validateCsrfToken()) {
            $this->outputErrors();
            return $this;
        }
        // authorize valid form fields
        foreach ($this->data as $field_name => $value) {
            if (!$this->validateFieldByName($field_name)) {
                unset($this->data[$field_name]);
            }
        }
        $this->is_valid = true;
        // validate form fields value
        foreach ($this->rules() as $field => $rules) {
            if (preg_match('/^\#\w+/', $field)) {
                continue;
            } else if (!isset($this->data[$field]) && ($rules['required'] ?? false)) {
                $this->is_valid = false;
                $this->errors[$field] = [
                    t(
                        'Field `@field_name` is required.',
                        ['field_name' => $this->getFieldName($field)]
                    )
                ];
            } else {
                $this->errors[$field] = $this->validateField($field, $rules);
                if (!empty($this->errors[$field])) {
                    $this->is_valid = false;
                }
            }
        }
        $this->outputErrors();
        return $this;
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

    /**
     * Manual remove form remembered values from session container.
     */
    public function complete(): void
    {
        foreach ($this->data as $key => $value) {
            session()->unset(self::SESSID . '/' . $key);
        }
        return;
    }

    protected function validateCsrfToken(): bool
    {
        if (static::SKIP_CSRF) {
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

    protected function validateFieldByName(string $field_name): bool
    {
        $rules = $this->rules();
        return isset($rules[$field_name]);
    }

    /**
     * @return array with errors if validation failed or empty array without errors if validation passed
     */
    protected function validateField(string $field_name, array $rules)
    {
        $validator = 'validateField' . pascalCase($rules['type']);
        unset($rules['type']);
        return $this->$validator($field_name, $rules);
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

    /**
     * Get raw value by field by name.
     * An alias for @method raw()
     */
    public function get(string $field_name)
    {
        return $this->raw($field_name);
    }

    /**
     * Get raw value by field by name
     * An alias for @method get()
     */
    public function raw(string $field_name): ?string
    {
        return $this->data[$field_name] ?? null;
    }

    protected function getFieldName(string $name): string
    {
        $rules = $this->rules();
        return t($rules[$name]['#label'] ?? $name);
    }
}
