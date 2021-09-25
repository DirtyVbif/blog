<?php

namespace Blog;

use Blog\Controller\BaseController;
use Blog\Modules\Response\Response;
use Blog\Modules\Router\Router;
use Blog\Modules\Template\Page;

class Blog
{
    use Components\Singletone,
        Components\BlogConfig,
        Components\TwigLoader;

    private Router $router;
    private Response $response;
    private BaseController $controller;
    private Page $page;

    public function __toString()
    {
        return (string)$this->response->render();
    }

    public function run(): void
    {
        $this->controller()->prepare();
    }

    public function response(): Response
    {
        if (!isset($this->response)) {
            $this->response = new Response;
        }
        return $this->response;
    }

    public function router(): Router
    {
        if (!isset($this->router)) {
            $this->router = new Router;
        }
        return $this->router;
    }

    public function controller(): BaseController
    {
        if (!isset($this->controller)) {
            $prefix = '\Blog\Modules\Controller\\';
            $name = $prefix . $this->router()->get('controller');
            $this->controller = class_exists($name) ? new $name : new \Blog\Controller\ErrorController;
        }
        return $this->controller;
    }

    public function page(): Page
    {
        if (!isset($this->page)) {
            $this->page = new Page;
        }
        return $this->page;
    }

    public function getLangcode(): string
    {
        return $this->router()->getLangcode();
    }
}
