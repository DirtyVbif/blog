<?php

namespace Blog\Controller;

use Blog\Modules\Entity\Comment;
use Blog\Modules\View\Blog;
use Blog\Request\CommentRequest;

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
        if ($type = $_POST['type'] ?? null) {
            $method = pascalCase("post request {$type}");
            if (method_exists($this, $method)) {
                $this->$method($_POST);
                return;
            }
        }
        pre($_POST);
        exit;
    }

    protected function postRequestComment(array $data): void
    {
        $request = new CommentRequest($data);
        if ($request->isValid()) {
            $comment = new Comment(0);
            $result = $comment->create($request);
        } else {
            $result = null;
        }
        if ($result) {
            msgr()->notice('Ваш комментарий отправлен и будет рассмотрен в ближайшее время.');
        } else if (!is_null($result)) {
            msgr()->error('При отправке комментария возникла ошибка. Если проблема повторяется, пожалуйста @contact_me.', ['contact_me' => '<a href="' . tpllink('contacts') . '">свяжитесь со мной</a>']);
        }
        $redirect = ($data['article_id'] ?? false) ? '/blog/' . $data['article_id'] : '<current>';
        app()->router()->redirect($redirect);
        return;
    }
}
