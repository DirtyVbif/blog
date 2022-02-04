<?php

namespace Blog\Controller;

class FrontController extends BaseController
{
    public function prepare(): void
    {
        parent::prepare();
        // add main page elements
        app()->page()->setAttr('class', 'page_front');
        // use front page styles
        app()->page()->useCss('front.min');
        // add page content
        app()->page()->addContent([
            // set front page banner
            app()->builder()->getBannerBlock(),
            // set front page skill box
            app()->builder()->getSkillsBlock(),
            // set front page summary block
            app()->builder()->getSummaryBlock(),
            // set front page blog preview block
            app()->builder()->getBlogPreview(),
            // set front page contacts block
            app()->builder()->getContactsBlock()
        ]);
        return;
    }

    public function getTitle(): string
    {
        return '';
    }

    public function postRequest(): void
    {
        pre($_POST);
        exit;
    }
}
