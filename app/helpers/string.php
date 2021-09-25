<?php

function strPascalCase(string $string): string
{
    $pascal_string = '';
    $parts = preg_split('/[\W]+/', $string);
    foreach ($parts as $p) {
        $pascal_string .= ucfirst(strtolower($p));
    }
    return $pascal_string;
}
