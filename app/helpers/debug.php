<?php

function debugFileCalled(): string
{
    $stack = debug_backtrace(1, 2);
    return $stack[1]['file'] . "::" . $stack[1]['line'];
}

/**
 * Main application debug helper with pre HTML-tag. Permanently prints debug output.
 * 
 * Can recieve multiple values;
 * Use `--v` flag in arguments for more verbosity;
 * 
 * @return void permanently prints debug output
 */
function pre(): void
{
    $verbouse = false;
    $arguments = func_get_args();
    $i = array_search('--v', $arguments, true);
    if ($i || $i === 0) {
        unset($arguments[$i]);
        $verbouse = true;
    }

    print "<pre style=\"color:#272727;font-weight:200;font-size:14px;padding:1px 10px;background:#e7e7e7;margin:0;white-space:pre-wrap;\"><code>";
    print "<hr><i>debug from: " . debugFileCalled() . "</i><br>";
    foreach ($arguments as $data) {
        $data = is_string($data) ? htmlspecialchars($data) : $data;
        ob_start();
        $verbouse ? var_dump($data) : print_r($data);
        $output = ob_get_clean();
        print "<div>$output</div>";
    }
    print "<hr></code></pre>";
    return;
}

/**
 * Get debug result output as string
 * 
 * Can recieve multiple values;
 * Use `--v` flag in arguments for more verbosity;
 * 
 * @return string debug output result
 */
function debug(): string
{
    $verbouse = false;
    $arguments = func_get_args();
    $i = array_search('--v', $arguments, true);
    if ($i || $i === 0) {
        unset($arguments[$i]);
        $verbouse = true;
    }

    ob_start();
    foreach ($arguments as $data) {
        $verbouse ? var_dump($data) : print_r($data);
    }
    return ob_get_clean();
}
