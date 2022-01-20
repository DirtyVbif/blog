<?php

use Blog\Client\CookiesFacade;
use Blog\Client\SessionFacade;

function app(): \Blog\Blog
{
    return \Blog\Blog::instance();
}

function page(): \Blog\Modules\Template\Page
{
    return app()->page();
}

function t(string $text): string
{
    return app()->translate($text);
}

/**
 * Get an object of `\Blog\Modules\FileSystem\File::class`
 */
function f(string $name, string $directory, ?string $extension = null, int $permission = 644): \Blog\Modules\FileSystem\File
{
    $file = new \Blog\Modules\FileSystem\File($name, $extension, $directory);
    $file->permissions($permission);
    return $file;
}

function session(): \Blog\Client\SessionFacade
{
    return SessionFacade::instance();
}

function cookies(): \Blog\Client\CookiesFacade
{
    return CookiesFacade::instance();
}

function tpllink(string $name): \Blog\Modules\TemplateFacade\Link
{
    return new \Blog\Modules\TemplateFacade\Link($name);
}

/**
 * Generate url offset with given @param string $path and @param array $parameters [optional].
 * 
 * @param string $path can be named path constant or relative offset
 */
function url(string $path, array $parameters = []): string
{
    $url = app()->router()->getUrl($path, $parameters);
    return $url;
}

function msgr(): \Blog\Modules\Messenger\Messenger
{
    return app()->messenger();
}
