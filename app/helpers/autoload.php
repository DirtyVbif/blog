<?php

$helpers = [
    'app.php',
    'debug.php'
];

foreach ($helpers as $helper_file) {
    require_once HELPERS . $helper_file;
}

return;
