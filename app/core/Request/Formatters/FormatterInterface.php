<?php

namespace Blog\Request\Formatters;

interface FormatterInterface
{
    /**
     * Formattes provided value and returns it
     * 
     * @return mixed $value formatted value
     */
    public function format($value): mixed;
}
