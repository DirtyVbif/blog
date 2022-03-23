<?php

namespace Blog\Request;

use Blog\Modules\CSRF\Token;
use Blog\Client\User;

/**
 * Each object remembers all provided values into session container and values must be cleared manualy.
 * 
 * If request wouldn't be valid then form keeps filled values from session container.
 * To clear remembered values must be called @method complete() manualy.
 */
abstract class RequestPrototype
{
    public const SESSID = 'last-request-data';
    public const EMAIL_PATTERN = '/^[\w\-\.]+@[\w\-^_]+\.[a-z]{2,}$/i';
    protected const ACCESS_LEVEL = 2;
    protected const CSRF_SKIP = false;

    protected bool $_is_valid;
    protected array $_errors = [];
    protected array $_validated_fields;
    protected bool $_validated;

    abstract function rules(): array;
    abstract function label(): string;
    
    public function __construct(
        protected array $_data = []
    ) {
        if (empty($this->_data)) {
            $this->_data = $_POST;
        }
    }

    /**
     * An alias for @method get(string $field_name)
     */
    public function __get(string $field_name)
    {
        return $this->get($field_name);
    }

    /**
     * Get validated field value by field name.
     * 
     * > It's an alias for magic @method __get(string $field_name).
     * > You can call $this->{$field_name} instead of $this->get($field_name).
     * > [optional] get raw value on null if second argument provided
     * 
     * @param bool $raw_on_null [optional] of TRUE @method get() will return raw value on null
     * @return mixed validated value or null
     */
    public function get(string $field_name, bool $raw_on_null = false)
    {
        if ($this->isValid() && isset($this->_validated_fields[$field_name])) {
            return $this->{$field_name};
        } else if ($raw_on_null) {
            return $this->_data[$field_name] ?? null;
        }
        return null;
    }

    /**
     * Checks if request data is valid
     */
    public function isValid(): bool
    {
        if (!$this->validated()) {
            $this->validate();
        }
        return $this->_is_valid ?? false;
    }

    protected function validated(): bool
    {
        return $this->_validated ?? false;
    }

    /**
     * Get raw field value by field name
     */
    public function raw(string $field_name): ?string
    {
        return $this->_data[$field_name] ?? null;
    }

    /**
     * Set new raw value by field name
     */
    public function set(string $field_name, $value): void
    {
        $this->_data[$field_name] = $value;
        return;
    }

    /**
     * Validation of request data to correspondes with specified rules for request.
     * 
     * After validation you must use @method complete() on success to clear remembered values from session container.
     * Reason is that provided values storing into session containers to let users not to fill form again on error.
     */
    public function validate(): self
    {
        if ($this->validated()) {
            return $this;
        }
        // set request as validated
        $this->_validated = true;
        // remember filled values for case if validation failed
        $this->rememberValues();
        if (!app()->user()->verifyAccessLevel(static::ACCESS_LEVEL)) {
            msgr()->error(t('You have no permission for that action. If you think that it\'s an error, please contact administrator.'));
            return $this;
        }
        // set pre-validations status as TRUE
        $this->_is_valid = true;
        // validate request CSRF-token
        if ($this->validateCsrfToken()) {
            // preprocess request data values
            $this->preprocessFields();
            // validate request data value
            $this->validateFields();
            // format request validated values
            $this->formatFields();
        }
        // output validation errors
        $this->outputErrors();
        return $this;
    }

    protected function validateCsrfToken(): bool
    {
        if (
            static::CSRF_SKIP
            || user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN)
            || user()->hasMasterIp()
        ) {
            return true;
        } else if (
            !isset($this->_data[Token::FORM_ID])
            || !app()->csrf()->validate($this->_data[Token::FORM_ID])
        ) {
            $this->_errors[Token::FORM_ID] = [
                t('CSRF-token is invalid. Please try again or contact administrator.')
            ];
            return $this->_is_valid = false;
        }
        unset($this->_data[Token::FORM_ID]);
        return true;
    }

    protected function parseAttribute(&$attr_key, &$attr_value, string $class): ?string
    {
        $pattern = '/^(' . $class . '\:+)([a-z][\w\-]+)/i';
        if (!preg_match($pattern, $attr_key) && !preg_match($pattern, $attr_value)) {
            return null;
        } else if (is_numeric($attr_key)) {
            $attr_key = $attr_value;
            $attr_value = null;
        }
        $method = preg_replace($pattern, '$2', $attr_key);
        return lcfirst(pascalCase($method));
    }

    protected function preprocessFields(): void
    {
        foreach ($this->rules() as $field_name => $rules) {
            foreach ($rules as $key => $argument) {
                if ($method = $this->parseAttribute($key, $argument, 'preprocessor')) {
                    $this->_data[$field_name] = !is_null($argument) ?
                        RequestPreprocessor::{$method}($argument, $field_name, $this)
                        : RequestPreprocessor::{$method}($field_name, $this);
                }
            }
        }
    }

    protected function validateFields(): void
    {
        foreach ($this->rules() as $field_name => $rules) {
            if (!isset($this->_errors[$field_name])) {
                $this->_errors[$field_name] = [];
            }
            $value = $this->raw($field_name);
            foreach ($rules as $key => $argument) {
                if ($method = $this->parseAttribute($key, $argument, 'validator')) {
                    $error = !is_null($argument) ?
                        RequestValidator::{$method}($value, $argument)
                        : RequestValidator::{$method}($value);
                    if ($error) {
                        array_push($this->_errors[$field_name], $error);
                    }
                }
            }
            if (empty($this->_errors[$field_name])) {
                $this->{$field_name} = $value;
                $this->_validated_fields[$field_name] = $field_name;
            } else {
                $this->_is_valid = false;
            }
        }
    }

    protected function formatFields(): void
    {
        if (!$this->isValid()) {
            return;
        }
        foreach ($this->rules() as $field_name => $rules) {
            foreach ($rules as $key => $argument) {
                if ($method = $this->parseAttribute($key, $argument, 'formatter')) {
                    $this->{$field_name} = !is_null($argument) ?
                        RequestFormatter::{$method}($argument, $this->{$field_name})
                        : RequestFormatter::{$method}($this->{$field_name});
                }
            }
        }
    }

    protected function outputErrors(): void
    {
        foreach ($this->_errors as $field_name => $field_errors) {
            foreach ($field_errors ?? [] as $error) {
                msgr()->error(
                    t(
                        $error,
                        ['field_name' => $this->getFieldName($field_name)]
                    )
                );
            }
        }
        return;
    }

    protected function getFieldName(string $name)
    {
        $rules = $this->rules();
        return $rules[$name]['#label'] ?? $name;
    }

    protected function rememberValues(): void
    {
        foreach ($this->_data as $key => $value) {
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
        foreach ($this->_data as $key => $value) {
            session()->unset(self::SESSID . '/' . $key);
        }
        return;
    }
}
