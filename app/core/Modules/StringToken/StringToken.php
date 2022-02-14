<?php

namespace Blog\Modules\StringToken;

class StringToken
{
    use Components\StringTokenMethods;

    protected const TOKEN_PATTERN = '/\:\[([a-z\|]+)\]/';
    protected static self $instance;

    public static function i(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    public static function parse(string $content): string
    {
        $tokens = [];
        if (preg_match_all(self::TOKEN_PATTERN, $content, $tokens)) {
            foreach ($tokens[1] as $i => $token) {
                $t_part = explode('|', $token);
                $method = 'getToken' . pascalCase(array_shift($t_part));
                if (!method_exists(self::i(), $method)) {
                    msgr()->debug("Can't parse token {$token}");
                    continue;
                }
                $tokens[1][$i] = self::i()->$method($t_part);
            }
            $content = str_replace($tokens[0], $tokens[1], $content);
        }
        return $content;
    }
}
