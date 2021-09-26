<?php

namespace Blog\Components;

use Twig\TwigFunction;

class ExtendedTwigFunctions
{
    protected array $functions;

    public function __construct()
    {
        $this->functions = [];
        $this->prepareExtendedFunctions([
            't'
        ]);
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
}
