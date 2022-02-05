<?php

namespace Blog\Controller;

class BlogController extends BaseController
{
    public function prepare(): void
    {
        parent::prepare();
        // add main page elements
        app()->page()->setAttr('class', 'page_blog');
        app()->page()->addContent([
            app()->builder()->getBlogPage()
        ]);
        return;
    }

    public function getTitle(): string
    {
        return t('blog');
    }

    public function postRequest(): void
    {
        pre($_POST);
        exit;
    }
}
