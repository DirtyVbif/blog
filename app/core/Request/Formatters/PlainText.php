<?php

namespace Blog\Request\Formatters;

#[\Attribute]
class PlainText implements FormatterInterface
{
    /**
     * @param string $value
     * 
     * @return string $value
     */
    public function format($value): mixed
    {
        $value = strip_tags($value);
        return $value;
    }
}
