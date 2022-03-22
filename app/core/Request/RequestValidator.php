<?php

namespace Blog\Request;

class RequestValidator
{
    public const MAIL_PATTERN = '/^[a-z][\w\-\.]+@[a-z][a-z0-9\-]+\.[a-z]\w+$/i';

    public static function type($value, string $type): ?string
    {
        if (!settype($value, $type)) {
            return "Field `@field_name` has wrong value can't be of type `{$type}`.";
        }
        return null;
    }

    public static function required($value, bool $required): ?string
    {
        if ($required && (empty($value) || !$value)) {
            return "Field `@field_name` is required.";
        }
        return null;
    }

    public static function pattern($value, string $pattern): ?string
    {
        if ($error = self::type($value, 'string')) {
            return $error;
        } else if (!preg_match($pattern, $value)) {
            return "Field `@field_name` contains invalid value or symbols.";
        }
        return null;
    }

    public static function strlenmin($value, int $min): ?string
    {
        if ($error = self::type($value, 'string')) {
            return $error;
        } else if (mb_strlen($value) < $min) {
            return "Field `@field_name` value length is lesser than {$min} symbols.";
        }
        return null;
    }

    public static function strlenmax($value, int $max): ?string
    {
        if ($error = self::type($value, 'string')) {
            return $error;
        } else if (mb_strlen($value) > $max) {
            return "Field `@field_name` value length is more than {$max} symbols.";
        }
        return null;
    }

    public static function email($value): ?string
    {
        if (self::pattern($value, self::MAIL_PATTERN)) {
            return "Field `@field_name` value must be of type `string` and contains correct e-mail address: <mail-box-name>@<host-name>.<domain>";
        }
        return null;
    }
}
