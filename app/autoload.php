<?php

/** @var string absolute path to the project folder on server. Includes trailing slash */
define('ROOTDIR', preg_replace('/(\\\|\/)app$/i', '/', __DIR__));
/** @var string relative path to the php application scripts folder. Includes trailing slash */
define('APPDIR', ROOTDIR . 'app/');
/** @var string relative path to the php application scripts folder. Includes trailing slash */
define('COREDIR', APPDIR . 'core/');
/** @var string relative path to the helper functions folder. Includes trailing slash */
define('HELPERS', APPDIR . 'helpers/');
/** @var string relative path to libraries */
define('LIBDIR', ROOTDIR . 'libraries/');

// include project helpers and utilities
$helpers = [
    'app.php',
    'debug.php',
    'string.php',
    'sql.php',
    'array.php',
    'html.php',
    'forge.php'
];

// load project helpers and utilities
foreach ($helpers as $helper_file) {
    require_once HELPERS . $helper_file;
}
