<?php

/** @var string relative path to the php application scripts folder. Includes trailing slash */
define('APPDIR', ROOTDIR . 'app/');
/** @var string relative path to the php application scripts folder. Includes trailing slash */
define('COREDIR', APPDIR . 'core/');
/** @var string relative path to the helper functions folder. Includes trailing slash */
define('HELPERS', APPDIR . 'helpers/');
/** @var string absolute path to the project folder on server. Includes trailing slash */
define('SERVERDIR', preg_replace('/(\\\|\/)app$/i', '', __DIR__));
/** @var string relative path to libraries */
define('LIBDIR', ROOTDIR . 'libraries/');

$namespaces = require_once APPDIR . 'namespaces.php';
$includes = require_once APPDIR . 'includes.php';

// project dependencies autoload script
$_dependencies_autoload = ROOTDIR . 'vendor/autoload.php';
if (file_exists($_dependencies_autoload)) {
    require_once $_dependencies_autoload;
}

// load project helpers and utilities
$_helpers_autoload = HELPERS . 'autoload.php';
if (file_exists($_helpers_autoload)) {
    require_once $_helpers_autoload;
}

// set class loader
spl_autoload_register('autoload');

/**
 * default project class loader function
 */
function autoload(string $class_required): void
{
    if (class_exists($class_required)) {
        return;
    }
    $class_name_string = preg_replace('/^\\\*/', '', $class_required);
    $namespace = array_map(
        fn ($part) => str_replace('_', DIRECTORY_SEPARATOR, ucfirst($part)),
        explode('\\', $class_name_string)
    );
    $vendor = array_shift($namespace);
    $classname = array_pop($namespace);
    $path = $GLOBALS['namespaces'][$vendor] ?? null;
    if (is_null($path)) {
        print '<pre>';
        print_r([
            'message' => "Can't load class <i>$class_name_string</i>",
            '$class_name_string' => $class_name_string,
            '$class_required' => $class_required,
            '$namespace' => $namespace,
            '$vendor' => $vendor,
            '$classname' => $classname,
            '$namespaces' => $GLOBALS['namespaces']
        ]);
        print '</pre>';
        die;
    }
    $path = str_replace('@classname', $classname, $path);
    $file = $path . implode(DIRECTORY_SEPARATOR, $namespace) . "/$classname.php";
    if (file_exists($file)) {
        require_once $file;
    }
}
