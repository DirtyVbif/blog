<?php

$helpers = [
    'app.php',
    'debug.php',
    'string.php'
];

foreach ($helpers as $helper_file) {
    require_once HELPERS . $helper_file;
}

return;
