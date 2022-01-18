<?php

namespace Blog\Components;

use Blog\Client\CookiesFacade;
use Blog\Client\SessionFacade;
use Blog\Database\Bridge;
use Blog\Modules\Builder\Builder;
use Blog\Modules\Response\Response;
use Blog\Modules\Router\Router;
use Blog\Modules\Template\Page;

trait BlogModules
{
    private Page $page;
    private Builder $builder;
    private Router $router;
    private Response $response;
    private Bridge $sql;

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

    public function page(): Page
    {
        if (!isset($this->page)) {
            $this->page = new Page;
        }
        return $this->page;
    }

    public function builder(): Builder
    {
        if (!isset($this->builder)) {
            $this->builder = new Builder;
        }
        return $this->builder;
    }

    public function sql(): Bridge
    {
        if (!isset($this->sql)) {
            $this->sql = new Bridge;
        }
        return $this->sql;
    }

    public function cookie(): CookiesFacade
    {
        return CookiesFacade::instance();
    }

    public function session(): SessionFacade
    {
        return SessionFacade::instance();
    }
}
