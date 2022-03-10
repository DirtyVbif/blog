<?php

namespace Blog\Request;

use Blog\Modules\CSRF\Token;
use Blog\Modules\User\User;
use ReflectionClass;
use ReflectionProperty;

/**
 * Each object remembers all provided values into session container and values must be cleared manualy.
 * 
 * If request wouldn't be valid then form keeps filled values from session container.
 * To clear remembered values must be called @method complete() manualy.
 */
class RequestPrototype
{
    public const SESSID = 'last-request-data';
    public const EMAIL_PATTERN = '/^[\w\-\.]+@[\w\-^_]+\.[a-z]{2,}$/i';
    protected const ACCESS_LEVEL = 2;
    protected const CSRF_SKIP = false;

    protected bool $_is_valid;
    protected array $_errors = [];
    protected array $_validated_fields;
    protected bool $_validated;
    protected ReflectionClass $_reflection;
    /** @var ReflectionProperty[] $_reflection_properties */
    protected array $_reflection_properties;
    
    public function __construct(
        protected array $_data = []
    ) {
        $this->_data = empty($data) ? $_POST : $data;
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
        // if validation will be failed status will be changed to FALSE
        $this->_is_valid = true;
        // validate request CSRF-token
        if ($this->validateCsrfToken()) {
            // preproccess request data values
            $this->preproccess();
            // validate request data value
            $this->validateAttributes();
            // format request validated values
            $this->postFormattersAttributes();
        }
        // output validation errors
        $this->outputErrors();
        return $this;
    }

    protected function validateCsrfToken(): bool
    {
        if (static::CSRF_SKIP || user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN)) {
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

    protected function preproccess(): void
    {
        foreach ($this->reflectionProperties() as $field_name => $property) {
            $attributes = $property->getAttributes(
                Preproccessors\PreproccessorInterface::class,
                \ReflectionAttribute::IS_INSTANCEOF
            );
            foreach ($attributes as $attribute) {
                /** @var Preproccessors\PreproccessorInterface $preproccessor */
                $preproccessor = $attribute->newInstance();
                $this->_data[$field_name] = $preproccessor->format($field_name, $this);
            }
        }
        return;
    }

    protected function validateAttributes(): void
    {
        foreach ($this->reflectionProperties() as $field_name => $property) {
            $attributes = $property->getAttributes(
                Validators\ValidatorInterface::class,
                \ReflectionAttribute::IS_INSTANCEOF
            );
            if (empty($attributes)) {
                continue;
            } else if (!isset($this->_errors[$field_name])) {
                $this->_errors[$field_name] = [];
            }
            $value = $this->raw($field_name);
            foreach ($attributes as $attribute) {
                /** @var Validators\ValidatorInterface $validator */
                $validator = $attribute->newInstance();
                if ($error = $validator->validate($value)) {
                    $this->_errors[$field_name][] = $error;
                }
            }
            if (empty($this->_errors[$field_name])) {
                $this->{$field_name} = $value;
                $this->_validated_fields[$field_name] = $field_name;
            } else {
                $this->_is_valid = false;
            }
        }
        return;
    }

    protected function postFormattersAttributes(): void
    {
        if (!$this->isValid()) {
            return;
        }
        foreach ($this->reflectionProperties() as $field_name => $property) {
            $attributes = $property->getAttributes(
                Formatters\FormatterInterface::class,
                \ReflectionAttribute::IS_INSTANCEOF
            );
            foreach ($attributes as $attribute) {
                /** @var Formatters\FormatterInterface */
                $formatter = $attribute->newInstance();
                $this->{$field_name} = $formatter->format($this->{$field_name});
            }
        }
        return;
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
        if (!isset($this->{$name})) {
            return $name;
        }
        $field = $this->reflection()->getProperty($name);
        $attributes = $field->getAttributes(RequestPropertyLabelAttribute::class);
        /** @var ?RequestPropertyLabelAttribute $label_attribute */
        $label_attribute = ($attributes[0] ?? null)?->newInstance();
        return $label_attribute?->get() ?? $name;
    }

    protected function reflection(): ReflectionClass
    {
        if (!isset($this->_reflection)) {
            $this->_reflection = new ReflectionClass(static::class);
        }
        return $this->_reflection;
    }

    /**
     * @return ReflectionProperty[]
     */
    protected function reflectionProperties(): array
    {
        if (!isset($this->_reflection_properties)) {
            foreach ($this->reflection()->getProperties() as $property) {
                if (empty($property->getAttributes())) {
                    continue;
                }
                $field_name = $property->getName();
                $this->_reflection_properties[$field_name] = $property;
            }
        }
        return $this->_reflection_properties;
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
