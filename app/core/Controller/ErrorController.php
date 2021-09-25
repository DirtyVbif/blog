<?php

namespace Blog\Controller;

class ErrorController extends BaseController
{
    public function prepare(): void
    {
        parent::prepare();
        $main_menu = app()->builder()->getMenu('main');
        app()->builder()->header()->set('menu', $main_menu);
        return;
    }

    public function getTitle(): string
    {
        return 'Error 404. Page not found.';
    }
}
