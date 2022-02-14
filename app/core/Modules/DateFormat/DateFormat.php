<?php

namespace Blog\Modules\DateFormat;

class DateFormat
{
    protected string $formatter;

    /**
     * @param string $format name of prepared formats:
     * * 'default' => DD of Month YYYY (eg 20 of February 1970)
     * * 'complete' => YYYY-MM-DDThh:mm:ssTZD (eg 1997-07-16T19:20:30+01:00)
     */
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
     * @return string DD of Month YYYY (eg 20 of February 1970)
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

    /**
     * @return string YYYY-MM-DDThh:mm:ssTZD (eg 1997-07-16T19:20:30+01:00)
     */
    protected function getCompleteFormat(): string
    {
        $date = date('Y-m-d', $this->unix_timestamp) . 'T' . date('H:i:sp', $this->unix_timestamp);
        return $date;
    }
}
