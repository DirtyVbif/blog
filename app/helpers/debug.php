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
 * > `--v` (verbouse) flag in arguments for more verbosity;
 * > `--q` (quit) flag for exit on complete;
 * > `--html` flag for encoding html chars in output;
 * 
 * @return void permanently prints debug output
 * @return never if flag `--q` provided
 */
function pre(): void
{
    $options = [
        'verbouse' => '--v',
        'html' => '--html',
        'quit' => '--q'
    ];
    $arguments = func_get_args();
    foreach ($options as $opt_name => $opt_arg) {
        $i = array_search($opt_arg, $arguments, true);
        $$opt_name = ($i || $i === 0);
        if ($$opt_name) {
            unset($arguments[$i]);
        }
    }
    print "<pre style=\"color:#272727;font-weight:200;font-size:14px;padding:1px 10px;background:#e7e7e7;margin:0;white-space:pre-wrap;\"><code>";
    print "<hr><i>debug from: " . debugFileCalled() . "</i><br>";
    foreach ($arguments as $data) {
        $data = is_string($data) ? htmlspecialchars($data) : $data;
        ob_start();
        $verbouse ? var_dump($data) : print_r($data);
        $output = ob_get_clean();
        print '<span>' . ($html ? htmlspecialchars($output) : $output) . '</span>';
    }
    print "<hr></code></pre>";
    if ($quit) {
        exit;
    }
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
