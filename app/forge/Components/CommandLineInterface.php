<?php

namespace BlogForge\Components;

class CommandLineInterface
{
    protected const OUTPUT_SUFFIX = '%s[%s]';
    protected const OUTPUT_SUFFIX_LENGTH = 12;
    protected const OUTPUT_TYPES_COLORS = [
        'notice' => 37,         // white
        'black' => 30,
        'error' => 31,          // red
        'success' => 32,        // green
        'warning' => 33,        // yellow
        'blue' => 34,
        'attention' => 35,      // magenta
        'cyan' => 36
    ];

    public function width(): int
    {
        $ansicon = $_SERVER['ANSICON'] ?? null;
        if (!$ansicon) {
            return 0;
        }
        $width = preg_replace('/^(\d+)(x\d+)(\s+\(\d+x\d+\))?$/', '$1', $ansicon);
        return (int)$width;
    }

    public function outputNotice(string $output): string
    {
        return $this->outputString($output);
    }

    public function outputSuccess(string $output): string
    {
        return $this->outputString($output, 'success');
    }

    public function outputError(string $output): string
    {
        return $this->outputString($output, 'error');
    }

    protected function outputString(string $output, string $type = 'notice'): string
    {
        return $this->wrapOutput($output, $type, 0);
    }

    protected function outputColor(string $type): string
    {
        $color = self::OUTPUT_TYPES_COLORS[$type] ?? 0;
        if ($color) {
            return "\e[{$color}m%s\e[0m";
        }
        return '%s';
    }

    public function colorizeString(string $string, string $type): string
    {
        return sprintf(
            $this->outputColor($type),
            $string
        );
    }

    public function wrapOutput(
        string $string,
        string $suffix = '',
        int $tab = 1,
        int $tab_size = 4
    ): string {
        $tab = max(0, $tab);
        $tab_size = max(1, $tab_size);
        $suffix_length = 0;
        if ($suffix_length = $this->strlen($suffix)) {
            $suffix_type = $suffix;
            $suffix_whitespaces = strpws(self::OUTPUT_SUFFIX_LENGTH - $suffix_length - 2);
            $suffix = strtoupper($suffix);
            $suffix = $this->colorizeString($suffix, $suffix_type);
            $suffix = sprintf(self::OUTPUT_SUFFIX, $suffix_whitespaces, $suffix);
            $suffix_length = self::OUTPUT_SUFFIX_LENGTH;
        } else {
            $suffix = '';
        }
        $prefix_length = $tab * $tab_size;
        $prefix = strpws($prefix_length);
        $str_length = $this->width() - $prefix_length - $suffix_length;
        $lines = $this->wrapString($string, $str_length, $prefix);
        return implode(PHP_EOL, $lines) . $suffix;
    }

    protected function wrapString(string $string, int $length, string $prefix = ''): array
    {
        if ($length < $this->strlen($string)) {
            $lines = [];
            $line = [];
            foreach (preg_split('/\s+/', $string) as $word) {
                $new_line = $line;
                $new_line[] = $word;
                if ($this->strlen(implode(' ', $new_line)) > $length) {
                    $new_line = implode(' ', $line);
                    $suffix = strpws($length - $this->strlen($new_line));
                    $lines[] = $prefix . $new_line . $suffix;
                    $line = [];
                    continue;
                }
                $line[] = $word;
            }
        } else {
            $suffix = strpws($length - $this->strlen($string));
            $lines = [$prefix . $string . $suffix];
        }
        return $lines;
    }

    protected function strlen(string $string): int
    {
        $string = preg_replace('/\e\[\d+m/', '', $string);
        return strlen($string);
    }
}
