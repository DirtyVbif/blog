<?php

namespace Blog\Request\Formatters;

use JetBrains\PhpStorm\ExpectedValues;

#[\Attribute]
class HtmlText implements FormatterInterface
{
    public const STRATEGY_FULL = 'full';
    public const STRATEGY_BASIC = 'basic';
    public const STRATEGY_CUSTOM = 'custom';
    protected const BASIC_TAGS = ['p', 'ul', 'ol', 'li', 'i', 'b', 'hr', 'code', 'h3', 'h4', 'h5', 'h6', 'em'];

    /**
     * @param string[]|string $custom_allowed_tags allowed html tags for `custom` html strategy
     */
    public function __construct(
        #[ExpectedValues([
            self::STRATEGY_FULL,
            self::STRATEGY_BASIC,
            self::STRATEGY_CUSTOM
        ])]
        protected string $html_strategy,
        protected array|string $custom_allowed_tags = []
    ) {
        
    }

    /**
     * @param string $value
     * 
     * @return string $value
     */
    public function format($value): mixed
    {
        switch ($this->html_strategy) {
            case self::STRATEGY_FULL:
                break;
            case self::STRATEGY_BASIC:
                $value = strip_tags($value, self::BASIC_TAGS);
                break;
            case self::STRATEGY_CUSTOM:
                $value = strip_tags($value, $this->custom_allowed_tags);
                break;
            default:
                $value = strip_tags($value);
        }
        $value = htmlspecialchars($value);
        return $value;
    }
}
