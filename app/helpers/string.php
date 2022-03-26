<?php

use Symfony\Component\Yaml\Yaml;

/**
 * Format input with prefix. Formats each value for arrays.
 * 
 * @param string|array $input string or array for formatting.
 * @param string $prefix string part to add or remove.
 * @param bool $remove if set false (defaul) given prefix will be added or removed if true.
 * @return string|array Formated string or array with prefix.
 */
function strPrefix($input, string $prefix, bool $remove = false)
{
    if (is_array($input)) {
        foreach ($input as $i => $string) {
            $input[$i] = strPrefix($string, $prefix);
        }
    } elseif (is_string($input)) {
        $pattern = "/^" . strRegexQuote($prefix) . "/";
        if (!preg_match($pattern, $input) && !$remove) {
            $input = $prefix . $input;
        } elseif (preg_match($pattern, $input) && $remove) {
            $input = preg_replace('/(^' . strRegexQuote($prefix) . ')(.*$)/', '$2', $input);
        }
    }
    return $input;
}

/**
 * Format input with suffix. Formats each value for arrays.
 * 
 * @param string|string[] $input string or array for formatting.
 * @param string $suffix string part to add or remove.
 * @param bool $remove if set false (defaul) given suffix will be added or removed if true.
 * @return string|string[] Formated string or array with suffix.
 */
function strSuffix($input, string $suffix, bool $remove = false)
{
    if (is_array($input)) {
        foreach ($input as $i => $string) {
            $input[$i] = strSuffix($string, $suffix);
        }
    } elseif (is_string($input)) {
        $pattern = '/' . strRegexQuote($suffix) . '$/';
        if (!preg_match($pattern, $input) && !$remove) {
            $input = $input . $suffix;
        } elseif (preg_match($pattern, $input) && $remove) {
            $input = preg_replace('/(^.*)(' . strRegexQuote($suffix) . '$)/', '$1', $input);
        }
    }
    return $input;
}

/**
 * Returns a version of str with a backslash character (\) before every character that is among these:
 * `. \ + * ? [ ^ ] ( $ )`
 * 
 * @link https://www.php.net/manual/en/function.quotemeta.php
 * 
 * @param string $string
 * @param array $chars - additional characters in array to be protected with a backslash character (\)
 */
function strRegexQuote(string $string, array $chars = ['/']): string
{
    $protected = ['.', '\\', '+', '*', '?', '[', '^', ']', '(', '$', ')'];
    $string = quotemeta($string);
    if (empty($chars)) {
        return $string;
    }
    foreach ($chars as $char) {
        if (in_array($char, $protected)) {
            continue;
        }
        $string = str_replace($char, "\\$char", $string);
    }
    return $string;
}

/**
 * Generates String of (str rand) Random symbols of specified length. Uses `[a-zA-Z0-9]` and `[@#$%_-*&?]` as additional symbols
 * 
 * @param int $length length of random string in symbols.
 * @param boolean $clear means if string will be clear of additional symbols `[@#$%_-*&?]` or not. String contains additional symbols by default.
 */
function strRand(int $length = 16, bool $clear = false): string
{
    $result = '';
    $i = 0;
    $chars = [
        'lc' => str_split('abcdefghijklmnopqrstuvwxyz'),
        'uc' => str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),
        'str' => str_split('aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ'),
        'num' => str_split('0123456789-_'),
        'signs' => str_split('0@1#2$3%4_5-6*7&8?9')
    ];

    if ($clear) {
        unset($chars['signs'], $chars['str']);
    }

    do {
        $x = array_rand($chars);
        $y = array_rand($chars[$x]);
        $result .= $chars[$x][$y];
        $i++;
    } while ($i < $length);

    return $result;
}

/**
 * Replace White Spaces in string (str rws) with single white space symbol
 */
function strrws(string $string): string
{
    return preg_replace('/[\s]+/', ' ', $string);
}

/**
 * Output string contains specified number of whitespaces
 */
function strpws(int $count): string
{
    $output = '';
    for ($i = 0; $i < max($count, 0); $i++) {
        $output .= ' ';
    }
    return $output;
}

/**
 * Format File (or directory) String (ffstr). Removes leading slash `/`
 * 
 * Recieves one or more string arguments as references and removes leading slash `/` for each argument
 */
function ffstr(string &...$args): void
{
    foreach ($args as &$arg) {
        if (preg_match('/^' . strRegexQuote(ROOTDIR) . '/i', $arg)) {
            continue;
        }
        $arg = preg_replace(
            ['/^(\/|\\\)+\b/', '/^(\/|\\\)*\.(\/|\\\)\b/'],
            ['', ''],
            $arg
        );
    }
    return;
}

/**
 * Formats Filename Path to absolute path on server
 * 
 * If directory name provided then formated path would have trailing slash `/`
 * 
 * @param string &...$args
 * @return void all provided pathes recives changes by reference
 */
function ffpath(string &...$args): void
{
    foreach ($args as &$path) {
        $path = str_replace('\\', '/', $path);
        $base = strPrefix(ROOTDIR, '/', true);
        $path = preg_replace('/^\/?' . strRegexQuote($base) . '/i', '', $path);
        $path = preg_replace('/^\.?\/+/', '', $path);
        if (!preg_match('/\w+\.[a-z0-9]+$/i', $path)) {
            $path = strSuffix($path, '/');
        }
        $path = ROOTDIR . $path;
    }
    return;
}

/**
 * Converts provided string `Some Example String` into PascalCase `ExampleInputString`
 * 
 * All non-word and non-numeric symbols will be trimmed.
 */
function pascalCase(string $string): string
{
    $pascal_string = '';
    $parts = preg_split('/[\W_]+/', $string);
    foreach ($parts as $p) {
        $pascal_string .= ucfirst(strtolower($p));
    }
    return $pascal_string;
}

/**
 * Converts provided string `Some Example String` into camelCase `exampleInputString`
 * 
 * All non-word and non-numeric symbols will be trimmed.
 */
function camelCase(string $string): string
{
    $string = pascalCase($string);
    return lcfirst($string);
}

/**
 * Converts provided string `Some Example String` into kebab-case `example-input-string`
 * 
 * All non-word and non-numeric symbols will be trimmed.
 */
function kebabCase(string $string, bool $transliterate = false, string $langcode = 'ru'): string
{
    $string = strtolower(
        ($transliterate ? transliterate($string, $langcode) : $string)
    );
    $output = preg_replace(
        '/(^\-*)|(\-*$)/',
        '',
        preg_replace('/[\W_]+/', '-', $string)
    );
    return $output;
}

/**
 * Converts provided string `Some Example String` into underscore_case `some_example_string`
 * 
 * All non-word and non-numeric symbols will be trimmed.
 */
function underscoreCase(string $string): string
{
    $output = preg_replace(
        '/(^_*)|(_*$)/',
        '',
        preg_replace('/[\W_]+/', '_', $string)
    );
    return strtolower($output);
}

/**
 * Converts absolute server path to relative app path
 */
function strTrimServDir(string $directory): string
{
    return str_replace(ROOTDIR, '', $directory);
}

function transliterate(string $input, string $langcode = 'ru'): string
{
    $source = APPDIR . 'translations/' . $langcode . '/transliteration.yml';
    $t = Yaml::parseFile($source);
    $output = str_replace($t[$langcode], $t['en'], $input);
    return $output;
}

/**
 * String token parser
 * 
 * Parse tokens `:[example|token]` and replace it with data
 */
function stok(string $content): string
{
    return \Blog\Modules\StringToken\StringToken::parse($content);
}

/**
 * Get name of class from full classname with vendor and namespace string
 * 
 * @param string $classname string with classname e.g.: `Vendor\Namespace\Classname`
 * @param object $classname any object which classname will be parsed
 * 
 * @return object
 * ```
 * {
 *  public ?string $vendor
 *  public ?string $namespace
 *  public string $classname
 * }
 * ```
 */
function parseClassname(string|object $classname): object
{
    if (!is_string($classname)) {
        /** @var string $classname */
        $classname = $classname::class;
    }
    $parts = preg_split('/\\\+/', $classname);
    $class = $vendor = $namespace = null;
    foreach ($parts as $i => $part) {
        if (!preg_replace('/\s*/', '', $part)) {
            unset($parts[$i]);
        }
    }
    $count = count($parts);
    if ($count > 1) {
        $vendor = array_shift($parts);
    }
    $class = array_pop($parts);
    if (count($parts) > 0) {
        $namespace = implode('\\', $parts);
    }
    
    return new class($vendor, $namespace, $class) {
        public function __construct(
            public ?string $vendor,
            public ?string $namespace,
            public string $classname
        ) {}

        public function __toString()
        {
            $array = [];
            if ($this->vendor) {
                $array[] = $this->vendor;
            }
            if ($this->namespace) {
                $array[] = $this->namespace;
            }
            $array[] = $this->classname;
            return implode('\\', $array);
        }
    };
}
