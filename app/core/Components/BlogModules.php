<?php

namespace Blog\Components;

use Blog\Client\CookiesFacade;
use Blog\Client\SessionFacade;
use Blog\Database\Bridge;
use Blog\Modules\CSRF\Token;
use Blog\Modules\Library\AbstractLibrary;
use Blog\Modules\Mailer\Mailer;
use Blog\Modules\PageBuilder\PageBuilder;
use Blog\Modules\Messenger\Messenger;
use Blog\Modules\Response\Response;
use Blog\Modules\Router\Router;
use Blog\Modules\Template\Page;
use Blog\Modules\User\User;
use Blog\Modules\View\BaseView;

trait BlogModules
{
    private Page $page;
    private PageBuilder $builder;
    private Router $router;
    private Response $response;
    private Bridge $sql;
    private Messenger $messenger;
    private Mailer $mailer;
    private User $user;
    private Token $csrf;
    /** @var BaseView[] $views */
    private array $views;
    /** @var AbstractLibrary[] $libraries */
    private array $libraries;

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

    public function mailer(): Mailer
    {
        if (!isset($this->mailer)) {
            $this->mailer = new Mailer;
        }
        return $this->mailer;
    }

    public function user(): User
    {
        if (!isset($this->user)) {
            $this->user = new User;
        }
        return $this->user;
    }

    public function csrf(): Token
    {
        if (!isset($this->csrf)) {
            $this->csrf = new Token;
        }
        return $this->csrf;
    }

    public function view(string $view_name): ?BaseView
    {
        if (!isset($this->views[$view_name])) {
            $view = '\\Blog\\Modules\\View\\' . pascalCase($view_name);
            if (class_exists($view)) {
                $this->views[$view_name] = new $view();
            } else {
                $this->views[$view_name] = null;
            }
        }
        return $this->views[$view_name];
    }

    public function library(string $name): ?AbstractLibrary
    {
        $classname = '\\BlogLibrary\\' . pascalCase($name);
        if (!class_exists($classname)) {
            return null;
        } else if (!isset($this->libraries[$classname])) {
            $this->libraries[$classname] = new $classname;
        }
        return $this->libraries[$classname];
    }
}
