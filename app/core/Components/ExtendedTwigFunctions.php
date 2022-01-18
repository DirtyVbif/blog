<?php

namespace Blog\Components;

use Twig\TwigFunction;

class ExtendedTwigFunctions
{
    protected array $functions = [];
    protected array $function_names = [
        't', 'link', 'url'
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
}
