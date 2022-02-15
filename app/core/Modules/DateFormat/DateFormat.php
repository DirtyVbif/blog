<?php

namespace Blog\Modules\DateFormat;

class DateFormat
{
    public const DEFAULT = 'default';
    public const COMPLETE = 'complete';
    public const DETAILED = 'detailed';

    protected const FORMATS = [
        0 => self::DEFAULT,
        1 => self::COMPLETE,
        2 => self::DETAILED
    ];

    protected string $formatter;

    /**
     * @param string $format name of prepared formats:
     * * 'default' => DD of Month YYYY (eg 01 of January 1970)
     * * 'detailed' => DD of Month YYYY hh:mm (eg 01 of January 1970 00:00)
     * * 'complete' => YYYY-MM-DDThh:mm:ssTZD (eg 1997-07-16T19:20:30+01:00)
     */
    public function __construct(
        protected int $unix_timestamp,
        $format = self::DEFAULT
    ) {
        $this->format($format);
    }

    public function __toString()
    {
        return (string)$this->render();
    }

    public function format(string $format): self
    {
        if (!in_array($format, self::FORMATS)) {
            $format = self::DEFAULT;
        }
        $this->formatter = 'get' . ucfirst(strtolower($format)) . 'Format';
        $this->format = $format;
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

    /**
     * @return string DD of Month YYYY hh:mm (eg 01 of January 1970 00:00)
     */
    protected function getDetailedFormat(): string
    {
        $time = date('H:i', $this->unix_timestamp);
        return $this->getDefaultFormat() . ' ' . $time;
    }
}
