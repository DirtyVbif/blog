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
        $foo_nav = app()->builder()->getMenu('footer');
        app()->builder()->footer()->set('menu', $foo_nav);
        $slider = app()->builder()->getSlider();
        app()->page()->addContent($slider);
        app()->page()->useCss('front.min');

        return;
    }

    public function getTitle(): string
    {
        return '';
    }
}
