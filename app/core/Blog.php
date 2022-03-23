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

    private const PROTECTED_PROPERTIES = ['env'];
    private BaseController $controller;

    public function __toString()
    {
        return (string)$this->response()->render();
    }

    public function __serialize(): array
    {
        $array = [];
        foreach ($this as $name => $value) {
            if (in_array($name, self::PROTECTED_PROPERTIES)) {
                continue;
            }
            $array[$name] = $value;
        }
        return $array;
    }

    public function __debugInfo()
    {
        return $this->__serialize();
    }

    public function run(): void
    {
        session()->start();
        $this->loadConfig();
        $this->controller()->prepare();
        // store last url must be placed after all main methods of building response
        $this->router()->storeLastUrl();
    }

    public function controller(?string $controller_name = null): BaseController
    {
        $prefix = '\Blog\Controller\\';
        if ($controller_name) {
            $controller_name = $prefix . pascalCase($controller_name) . 'Controller';
            if (class_exists($controller_name)) {
                return new $controller_name;
            } else {
                $this->controller = $this->controller('error');
            }
        }
        if (!isset($this->controller)) {
            $name = $prefix . $this->router()->get('controller');
            $this->controller = class_exists($name) ? new $name : new \Blog\Controller\CustomController;
        }
        return $this->controller;
    }

    public function getLangcode(): string
    {
        return $this->router()->getLangcode();
    }
}
