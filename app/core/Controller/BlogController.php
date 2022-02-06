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
        app()->page()->setAttr('class', 'page_blog');
        // use blog page styles
        app()->page()->useCss('blog.min');
        return;
    }

    protected function validateRequest(): bool
    {
        $container_class = 'container_blog';
        // blog controller recieves only 1 argument
        if ($sub_argument = app()->router()->arg(3)) {
            // every url offset of 3rd level is unexisting
            return false;
        } else if ($argument = app()->router()->arg(2)) {
            // check argument for matching with blog article
            $view = new Blog;
            /** @var \Blog\Modules\TemplateFacade\BlogArticle $article */
            if (is_numeric($argument) && $article = $view->getArticleById($argument)) {
                // if argument is numeric and equals to blog article id then redirect to named url alias of that article
                app()->router()->redirect($article->url);
                return true;
            } else if (is_numeric($argument)) {
                // if argument is numeric and there is no blog article with such id then url is unexisting
                return false;
            }
            // try to load article by url alias
            $article = $view->getArticleByAlias($argument);
            if (!$article) {
                return false;
            }
            // view loaded blog article
            $content = $article;
            app()->page()->setTitle($article->title);
            $container_class = 'container_article';
        } else {
            // if no arguments then load blog page view
            $content = app()->builder()->getBlogPage();
        }
        app()->page()->addContent($content);
        app()->page()->content()->addClass($container_class);
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
