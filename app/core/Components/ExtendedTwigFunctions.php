<?php

namespace Blog\Components;

use Twig\TwigFunction;

class ExtendedTwigFunctions
{
    protected array $functions = [];
    protected array $function_names = [
        't', 'link', 'url', 'html id'
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
            $fn = 'initFunction' . strPascalCase($name);
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
                $output = "id={$id}";
            }
            /** @return string $output */
            return $output;
        });
    }
}
