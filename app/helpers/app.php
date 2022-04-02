<?php

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
function f(
    string $name,
    ?string $directory = null,
    ?string $extension = null,
    int $permissions = 0644
): \Blog\Modules\FileSystem\File {
    $file = new \Blog\Modules\FileSystem\File($name, $directory, $extension);
    $file->permissions($permissions);
    return $file;
}

function session(): \Blog\Client\SessionFacade
{
    return \Blog\Client\SessionFacade::instance();
}

function cookies(): \Blog\Client\CookiesFacade
{
    return \Blog\Client\CookiesFacade::instance();
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
    return app()->router()->host() . strPrefix($offset, '/');
}

function msgr(): \Blog\Modules\Messenger\Messenger
{
    return app()->messenger();
}

function user(): \Blog\Client\User
{
    return app()->user();
}

function consoleLog(string $type, string $message): void
{
    app()->logger()->console($type, $message);
    return;
}

function systemLog(string $type, string $text, array $data = []): void
{
    app()->logger()->log($type, $text, $data);
    return;
}

function template(string $tag = 'div', string $name = 'template'): \Blog\Modules\Template\Template
{
    return new \Blog\Modules\Template\Template($tag, $name);
}

function tpl(string $tag = 'div', string $name = 'template'): \Blog\Modules\Template\Template
{
    return template($tag, $name);
}
