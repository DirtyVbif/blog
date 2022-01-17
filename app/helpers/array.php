<?php

/**
 * Set Array Multi-Deep Value (setamdv)
 * 
 * Store given value into multideep array by keys 
 * * [optional] and rewrite existing value if specified
 * 
 * @param array $k multi-deep array keys that must be specified or rewrite
 * @param mixed $container multi-deep array that must contains given value with specified array keys
 * @param mixed $value that must be stored into `$container`
 * @param bool $rewrite boolean trigger to rewrite existing value or not
 * @param bool $unset boolean trigger if unset required for specified array key
 * 
 * @return mixed
 */
function setamdv(array $k, $container, $value = null, bool $rewrite = true, bool $unset = false)
{
    // case to unset variable from array
    if ($unset && count($k) == 1) {
        $k = $k[0];
        unset($container[$k]);
        return $container;
    }
    // case when last level of multidimensional array reached
    if (empty($k)) {
        if (!$rewrite && !is_null($container)) {
            $value = [$container, $value];
        }
        return $value;
    }
    // case when keys array not empty
    $key = array_shift($k);
    if ($unset && !isset($container[$key])) {
        // if uset required and current key is not set end recursion
        return $container;
    } elseif (!is_array($container)) {
        // if current container is not array but must be an array
        $i = $key == 0 ? 1 : 0;
        $container = !is_null($container) ? [$i => $container] : [];
    }
    // proceed next loop of recursion
    $container[$key] = setamdv($k, $container[$key] ?? null, $value, $rewrite, $unset);
    // return modified container
    return $container;
}
