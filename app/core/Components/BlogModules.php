<?php

namespace Blog\Components;

use Blog\Client\CookiesFacade;
use Blog\Client\SessionFacade;
use Blog\Database\Bridge;
use Blog\Modules\PageBuilder\PageBuilder;
use Blog\Modules\Messenger\Messenger;
use Blog\Modules\Response\Response;
use Blog\Modules\Router\Router;
use Blog\Modules\Template\Page;

trait BlogModules
{
    private Page $page;
    private PageBuilder $builder;
    private Router $router;
    private Response $response;
    private Bridge $sql;
    private Messenger $messenger;

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

    public function builder(): PageBuilder
    {
        if (!isset($this->builder)) {
            $this->builder = new PageBuilder;
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

    public function messenger(): Messenger
    {
        if (!isset($this->messenger)) {
            $this->messenger = new Messenger;
        }
        return $this->messenger;
    }
}
