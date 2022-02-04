<?php

namespace Blog\Modules\DateFormat;

class DateFormat
{
    protected string $formatter;

    public function __construct(
        protected int $unix_timestamp,
        $format = 'default'
    ) {
        $this->format($format);
    }

    public function __toString()
    {
        return (string)$this->render();
    }

    public function format(string $format): self
    {
        $formatter = 'get' . ucfirst(strtolower($format)) . 'Format';
        if (method_exists($this, $formatter)) {
            $this->formatter = $formatter;
            $this->format = $format;
        } else {
            return $this->format('default');
        }
        return $this;
    }

    public function render(): string
    {
        return $this->{$this->formatter}();
    }

    /**
     * @return string DD <month_full> YYYY
     */
    protected function getDefaultFormat(): string
    {
        // set day of month DD
        $date = date('d', $this->unix_timestamp);
        // set full month name
        $date .= ' ' . t('of ' . date('F', $this->unix_timestamp));
        // set year number YYYY
        $date .= ' ' . date('Y', $this->unix_timestamp);
        return $date;
    }
}
