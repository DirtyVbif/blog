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
        // 'Path\To\Class' => ['html', 'js', 'all']
        'Blog\Modules\Template\BaseTemplate' => ['html'],
        'Blog\Modules\Template\TemplateWrapper' => ['html'],
        'Blog\Modules\Template\TemplateAttributes' => ['html'],
        'Blog\Modules\TemplateFacade\Title' => ['html']
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

return $__config;
