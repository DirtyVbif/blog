<?php

namespace Blog\Components;

use Twig\Markup;
use Twig\TwigFunction;

class ExtendedTwigFunctions
{
    protected array $functions = [];
    protected array $function_names = [
        't', 'link', 'url', 'html id', 'img', 'csrf', 'use svg',
        'classlist to string', 'old', 'import file content'
    ];

    public function __construct()
    {
        $this->prepareExtendedFunctions($this->function_names);
    }

    public function list(): array
    {
        return $this->functions;
    }

    protected function prepareExtendedFunctions(array $list): self
    {
        foreach ($list as $name) {
            $fn = 'initFunction' . pascalCase($name);
            $this->functions[$name] = $this->$fn();
        }
        return $this;
    }

    protected function initFunctionT(): TwigFunction
    {
        return new TwigFunction('t', 't');
    }

    protected function initFunctionLink(): TwigFunction
    {
        return new TwigFunction('link', 'tpllink');
    }

    protected function initFunctionUrl(): TwigFunction
    {
        return new TwigFunction('url', 'url');
    }

    protected function initFunctionHtmlId(): TwigFunction
    {
        return new TwigFunction('id', function(string $id, bool $render_as_attr = false) {
            /** @var string $output */
            $output = $id;
            app()->builder()->useId($id);
            if ($render_as_attr) {
                $output = "id=\"{$id}\"";
            }
            return new Markup($output, CHARSET);
        });
    }

    protected function initFunctionImg(): TwigFunction
    {
        return new TwigFunction('img', 'img');
    }

    protected function initFunctionCsrf(): TwigFunction
    {
        return new TwigFunction('csrf', 'csrf');
    }

    protected function initFunctionClasslistToString(): TwigFunction
    {
        return new TwigFunction('classlistToString', 'classlistToString');
    }

    protected function initFunctionOld(): TwigFunction
    {
        return new TwigFunction('old', 'old');
    }

    protected function initFunctionImportFileContent(): TwigFunction
    {
        return new TwigFunction('importFileContent', function(string $filename): ?string {
            $file = f($filename);
            if ($file->exists()) {
                return new \Twig\Markup($file->content(), CHARSET);
            }
            return null;
        });
    }

    protected function initFunctionUseSvg(): TwigFunction
    {
        return new TwigFunction('usesvg', 'usesvg');
    }
}
