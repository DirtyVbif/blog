<?php

namespace Blog\Controller;

class FrontController extends BaseController
{
    public function prepare(): void
    {
        parent::prepare();
        // add main page elements
        app()->page()->setAttr('class', 'page_front');
        $main_menu = app()->builder()->getMenu('main');
        app()->builder()->header()->set('menu', $main_menu);
        $foo_nav = app()->builder()->getMenu('footer');
        app()->builder()->footer()->set('menu', $foo_nav);
        // use front page styles
        app()->page()->useCss('front.min');
        // add page content
        app()->page()->addContent([
            // set front page slider
            app()->builder()->getSlider(),
            // set front page skill box
            app()->builder()->getSkills()
        ]);
        return;
    }

    public function getTitle(): string
    {
        return '';
    }
}
