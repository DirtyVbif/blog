<?php

namespace Blog\Controller;

use Blog\Modules\View\Blog;

class BlogController extends BaseController
{
    protected \Blog\Modules\Template\Element $content;

    public function prepare(): void
    {
        parent::prepare();
        if (!$this->validateRequest()) {
            // if blog arguments is invalide then load error controller with status 404
            app()->controller('error')->prepare();
            return;
        }
        app()->page()->addClass('page_blog');
        // use blog page styles
        app()->page()->useCss('blog.min');
        return;
    }

    protected function validateRequest(): bool
    {
        // blog controller recieves only 1 argument
        if ($sub_argument = app()->router()->arg(3)) {
            // every url offset of 3rd level is unexisting
            return false;
        }
        if ($argument = app()->router()->arg(2)) {
            return Blog::viewBlogArticle($argument);
        }        
        Blog::viewBlogPage();
        return true;
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
