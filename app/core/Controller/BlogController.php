<?php

namespace Blog\Controller;

use Blog\Modules\TemplateFacade\Form;
use Blog\Modules\View\Blog;

class BlogController extends BaseController
{
    use Components\BlogControllerComments,
        Components\BlogControllerPostRequests;

    protected \Blog\Modules\Template\Element $content;

    protected int $status;

    public function prepare(): void
    {
        parent::prepare();
        if (!$this->validateRequest()) {
            // if access denied
            /** @var ErrorController $err_c */
            $conerr = app()->controller('error');
            $conerr->prepare($this->status);
            return;
        }
        app()->page()->addClass('page_blog');
        // use blog page styles
        app()->page()->useCss('/css/blog.min');
        return;
    }

    protected function validateRequest(): bool
    {
        $this->status = 200;
        if ($argument = app()->router()->arg(2)) {
            // check if controller method requested
            $method = pascalCase("get request {$argument}");
            if (method_exists($this, $method)) {
                // use specified controller method
                return $this->$method();
            } else if ($sub_argument = app()->router()->arg(3)) {
                // every other url offset of 3rd level without controller method is unexisting
                $this->status = 404;
                return false;
            }
            return Blog::viewBlogArticle($argument);
        }
        // if no arguments passed to blog controller then render blog articles list
        // set page meta
        app()->page()->setMetaTitle(stok('Журнал веб-разработчика | :[site]'));
        app()->page()->setMeta('description', [
            'name' => 'description',
            'content' => 'Полезные и интересные материалы и статьи в персональном блоге веб-разработчика'
        ]);
        Blog::viewBlogPage();
        return true;
    }

    public function getTitle(): ControlledPageTitle
    {
        parent::getTitle();
        if (!$this->title->isset()) {
            $this->title->set(t('blog'));
        }
        return $this->title;
    }

    protected function getRequestCreate(): bool
    {
        // verify user access level
        if (!app()->user()->verifyAccessLevel(4)) {
            $this->status = 403;
            return false;
        }
        $form = new Form('blog-article');
        // app()->page()->setTitle('Создание нового материала для блога');
        $this->getTitle()->set('Создание нового материала для блога');
        app()->page()->addContent($form);
        return true;
    }
}
