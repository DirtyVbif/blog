<?php

/**
 * Default application charset
 */
const CHARSET = 'UTF-8';

/**
 * Application config array
 * @var array
 */
$__config = [];

/**
 * Default application langcode
 */
$__config['langcode'] = 'ru';

/**
 * Twig settings
 */
$__config['twig'] = [
    'safe_classes' => [
        // 'Path\To\Class' => ['html', 'js', 'all']
        'Blog\Modules\Template\BaseTemplate' => ['html'],
        'Blog\Modules\Template\BaseTemplateElement' => ['html'],
        'Blog\Modules\TemplateFacade\TemplateFacade' => ['html']
    ],
    'config' => [
        // 'cache' => ROOTDIR . 'cache/templates',
        'cache' => false,
        'debug' => true,
        'auto_reload' => false
        // 'autoescape' => false
    ],
    'templates' => 'templates'
];

/**
 * Database connection settings
 */
$__config['database'] = [
    'HOST' => 'localhost',
    'NAME' => 'mublog',
    'USER' => 'root',
    'PASS' => 'root',
    'DRIVER' => 'mysql'
];

/**
* PDO connection parameters.
* This parameters will be used if 'PDO' database strategy selected
*/
$__config['pdo'] = [
   PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // return assoc array
   PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION           // return exeption if error
];

/**
 * Application cache configurations
 */
$__config['cache'] = [
    'sql' => [
        'status' => true,
        'lifetime' => 60 * 60 * 24,
        'minimized' => false
    ]
];

return $__config;
