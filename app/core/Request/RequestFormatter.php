<?php

namespace Blog\Request;

class RequestFormatter
{
    public const STRATEGY_FULL = 'full';
    public const STRATEGY_BASIC = 'basic';
    protected const BASIC_TAGS = ['p', 'ul', 'ol', 'li', 'i', 'b', 'hr', 'code', 'h3', 'h4', 'h5', 'h6', 'em'];

    public static function plainText(string $value): string
    {
        $value = strip_tags($value);
        return $value;
    }

    public static function htmlText(string $strategy, string $value): string
    {
        switch ($strategy) {
            case self::STRATEGY_FULL:
                break;
            case self::STRATEGY_BASIC:
                $value = strip_tags($value, self::BASIC_TAGS);
                $value = strip_attributes($value);
                break;
            default:
                $value = strip_tags($value);
        }
        return $value;
    }
}
