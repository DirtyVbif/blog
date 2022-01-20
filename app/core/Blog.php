<?php

namespace Blog;

use Blog\Controller\BaseController;

class Blog
{
    use Components\Singletone,
        Components\BlogConfig,
        Components\TwigLoader,
        Components\Translator,
        Components\BlogModules;

    private BaseController $controller;

    public function __toString()
    {
        return (string)$this->response()->render();
    }

    public function run(): void
    {
        session()->start();
        $this->loadConfig();
        $this->controller()->prepare();
    }

    public function controller(): BaseController
    {
        if (!isset($this->controller)) {
            $prefix = '\Blog\Controller\\';
            $name = $prefix . $this->router()->get('controller');
            $this->controller = class_exists($name) ? new $name : new \Blog\Controller\ErrorController;
        }
        return $this->controller;
    }

    public function getLangcode(): string
    {
        return $this->router()->getLangcode();
    }
}
