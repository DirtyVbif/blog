<?php

namespace Blog\Request\Validators;

#[\Attribute]
class StringLength implements ValidatorInterface
{
    public function __construct(
        protected int $max_length,
        protected int $min_length = 0
    ) {
        $this->max_length = max(1, $this->max_length);
        $this->min_length = max(0, $this->min_length);
    }

    /**
     * @param string $value
     */
    public function validate($value): ?string
    {
        if (!settype($value, 'string')) {
            return "Field `@field_name` has wrong value type. It must be of type `string`.";
        }
        $length = mb_strlen($value);
        if ($length > $this->max_length) {
            return "Field `@field_name` value is longer than {$this->max_length} symbols.";
        } else if ($length < $this->min_length) {
            return "Field `@field_name` value length is lesser than {$this->min_symbols} symbols.";
        }
        return null;
    }
}
