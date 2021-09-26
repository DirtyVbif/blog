<?php

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
 * @param string $string
 * @param array $chars - addition characters in array to be protected with a backslash
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

function strPascalCase(string $string): string
{
    $pascal_string = '';
    $parts = preg_split('/[\W]+/', $string);
    foreach ($parts as $p) {
        $pascal_string .= ucfirst(strtolower($p));
    }
    return $pascal_string;
}
