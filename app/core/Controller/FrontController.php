<?php

namespace Blog\Controller;

class FrontController extends BaseController
{
    public function prepare(): void
    {
        parent::prepare();
        app()->page()->setAttr('class', 'page_front');
        $main_menu = app()->builder()->getMenu('main');
        app()->builder()->header()->set('menu', $main_menu);
        return;
    }

    public function getTitle(): string
    {
        return 'Blog page';
    }
}
