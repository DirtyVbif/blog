<?php

/** @var string ROOTDIR absolute path to the project folder on server. Includes trailing slash `/` */
define('ROOTDIR', str_replace('\\', '/', preg_replace('/(\\\|\/)app$/i', '/', __DIR__)));
/** @var string APPDIR absolute path to the php application root folder. Includes trailing slash `/` */
define('APPDIR', ROOTDIR . 'app/');
/** @var string COREDIR absolute path to the php application core folder. Includes trailing slash `/` */
define('COREDIR', APPDIR . 'core/');
/** @var string HELPERS absolute path to the helper functions folder. Includes trailing slash `/` */
define('HELPERS', APPDIR . 'helpers/');
/** @var string LIBDIR absolute path to libraries. Includes trailing slash `/` */
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
