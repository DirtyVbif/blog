<?php

/** @var string CHARSET Default application charset */
const CHARSET = 'UTF-8';
/** @var string PUBNAME name of public directory on server */
const PUBNAME = 'public';
/** @var string PUBDIR absolute path to public (html, www) directory. Includes trailing slash `/` */
const PUBDIR = ROOTDIR . PUBNAME . '/';

/**
 * @var array<string, string|array<string, mixed>> $__config Application config array
 */
$__config = [];

/**
 * Default application langcode
 */
$__config['langcode'] = 'ru';

/**
 * Webmaster contacts
 */
$__config['webmaster'] = [
    'mail' => 'info@mublog.site',
    'ip' => '127.0.0.1'
];

/**
 * Application development settings
 */
$__config['development'] = [
    'js' => true,
    'livereload' => false
];

/**
 * Twig settings
 */
$__config['twig'] = [
    'safe_classes' => [
        // 'Path\To\Class' => ['html', 'js', 'all']
        'Blog\Modules\Template\BaseTemplate' => ['html'],
        'Blog\Modules\Template\BaseTemplateElement' => ['html'],
        'Blog\Modules\TemplateFacade\TemplateFacade' => ['html'],
        'Blog\Interface\TemplateInterface' => ['html'],
        'Blog\Modules\Template\RenderableElement' => ['html']
    ],
    'config' => [
        // 'cache' => PUBDIR . 'cache/twig',
        'cache' => false,
        'debug' => false,
        'auto_reload' => false
        // 'autoescape' => false
    ],
    'templates' => ROOTDIR . 'templates'
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
$__config['cache_directory'] = PUBDIR . 'cache/';
$__config['cache'] = [
    'sql' => [
        'status' => true,
        'lifetime' => 3600,
        'minimized' => true
    ]
];

/**
 * User authentification configurations
 */
$__config['user'] = [
    'utoken_lifetime' => 3600 * 24 * 7,
    'utoken_timeout' => 60 * 5,
    'csrf_token_lifetime' => 60 * 15
];

return $__config;
