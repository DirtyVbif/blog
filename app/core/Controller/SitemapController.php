<?php

namespace Blog\Controller;

use Blog\Modules\Sitemap\Sitemap;
use Blog\Client\User;

class SitemapController extends BaseController
{
    protected int $status = 200;

    public function prepare(): void
    {
        // parent::prepare();
        if (!$this->validateRequest()) {
            app()->controller('error')->prepare($this->status);
            return;
        }
        return;
    }

    protected function validateRequest(): bool
    {
        if ($argument = app()->router()->arg(2)) {
            $method = pascalCase("get request {$argument}");
            if (!method_exists($this, $method)) {
                $this->status = 404;
                return false;
            }
            return $this->$method();
        }
        return false;
    }

    public function postRequest(): void
    {
        pre($_POST);
        die;
    }

    protected function getRequestGenerate(): bool
    {
        if (!app()->user()->verifyAccessLevel(User::ACCESS_LEVEL_MASTER)) {
            $this->status = 403;
            return false;
        }
        Sitemap::generate();
        app()->router()->redirect('<previous>');
        return true;
    }
}
