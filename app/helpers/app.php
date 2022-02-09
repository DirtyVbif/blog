<?php

use Blog\Client\CookiesFacade;
use Blog\Client\SessionFacade;
use Twig\Markup;

function app(): \Blog\Blog
{
    return \Blog\Blog::instance();
}

function page(): \Blog\Modules\Template\Page
{
    return app()->page();
}

function t(string $text, array $variables = []): string
{
    return app()->translate($text, $variables);
}

/**
 * Get an object of `\Blog\Modules\FileSystem\File::class`
 */
function f(string $name, string $directory, ?string $extension = null, int $permissions = 644): \Blog\Modules\FileSystem\File
{
    $file = new \Blog\Modules\FileSystem\File($name, $directory, $extension);
    $file->permissions($permissions);
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

function tpllink(string $name, ?string $hash_base_path = null): \Blog\Modules\TemplateFacade\Link
{
    return new \Blog\Modules\TemplateFacade\Link($name, $hash_base_path);
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

function fullUrlTo(string $offset = '/'): string
{
    return app()->router()->domain() . strPrefix($offset, '/');
}

function msgr(): \Blog\Modules\Messenger\Messenger
{
    return app()->messenger();
}

function img(string $src): \Blog\Modules\TemplateFacade\Image
{
    $img = new \Blog\Modules\TemplateFacade\Image($src);
    return $img;
}

function csrf(bool $render = true)
{
    if ($render) {
        return new Markup(app()->csrf()->render(), CHARSET);
    }
    return app()->csrf();
}
