<?php

namespace Blog\Controller;

use Blog\Modules\Entity\Comment;
use Blog\Modules\View\Blog;
use Blog\Request\CommentRequest;

class BlogController extends BaseController
{
    protected \Blog\Modules\Template\Element $content;

    protected int $status;

    public function prepare(): void
    {
        parent::prepare();
        if (!$this->validateRequest()) {
            // if blog arguments is invalide then load error controller with status 404
            /** @var ErrorController $err_c */
            $err_c = app()->controller('error');
            $err_c->prepare($this->status);
            return;
        }
        app()->page()->addClass('page_blog');
        // use blog page styles
        app()->page()->useCss('blog.min');
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

    protected function postRequestCommentAdd(array $data): void
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

    protected function getRequestComment(): bool
    {
        // verify user access level
        if (!app()->user()->verifyAccessLevel(4)) {
            $this->status = 403;
            return false;
        }
        // check request arguments
        $cid = app()->router()->arg(3);
        $action = app()->router()->arg(4);
        if (!$cid || !$action) {
            $this->status = 404;
            return false;
        }
        $action = pascalCase($action);
        $comment = new Comment($cid);
        if (!$comment->exists() || !method_exists($comment, $action)) {
            $this->status = 404;
            return false;
        }
        $comment->$action();
        app()->router()->redirect('<previous>');
        return true;
    }
}
