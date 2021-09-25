<?php

/**
 * Application global charset
 */
const CHARSET = 'UTF-8';

/**
 * Application config array
 * @var array
 */
$__config = [];

// set default application parameters
$__config['langcode'] = 'ru';

// set twig settings
$__config['twig'] = [
    'safe_classes' => [
        // 'DMPF\Modules\Template\Page' => ['html', 'js', 'all']
        // 'DMPF\Modules\Template\Wrapper' => ['html']
    ],
    'config' => [
        'cache' => 'cache/templates',
        // 'cache' => false,
        'debug' => true,
        'auto_reload' => false
        // 'autoescape' => false
    ],
    'templates' => 'templates'
];

return $__config;
