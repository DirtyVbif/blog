<?php

namespace Blog\Controller;

class BlogController extends BaseController
{
    public function prepare(): void
    {
        parent::prepare();
        // add main page elements
        app()->page()->setAttr('class', 'page_blog');
        // use front page styles
        app()->page()->useCss('blog.min');
        // add blog page content
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
