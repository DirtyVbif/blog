<?php

namespace Blog\Controller\Components;

use Blog\Modules\Entity\Article;
use Blog\Modules\Entity\Comment;
use Blog\Request\RequestFactory;

trait BlogControllerPostRequests
{
    public function postRequest(): void
    {
        if ($type = $_POST['type'] ?? null) {
            $method = pascalCase("post request {$type}");
            if (method_exists($this, $method)) {
                $this->$method();
                return;
            }
        }
        pre([
            'error' => 'no method for BlogController::postRequest()',
            'data' => $_POST
        ]);
        exit;
    }

    protected function postRequestCommentAdd(): void
    {
        $request = RequestFactory::get('comment');
        if ($request->isValid()) {
            $result = Comment::create($request);
        } else {
            $result = null;
        }
        if ($result) {
            msgr()->notice('Ваш комментарий отправлен и будет рассмотрен в ближайшее время.');
        } else if (!is_null($result)) {
            msgr()->error('При отправке комментария возникла ошибка. Если проблема повторяется, пожалуйста @contact_me.', ['contact_me' => '<a href="' . tpllink('contacts') . '">свяжитесь со мной</a>']);
        }
        app()->router()->redirect('<current>');
        return;
    }

    protected function postRequestBlogArticleCreate(): void
    {
        // verify user access level
        if (!app()->user()->verifyAccessLevel(4)) {
            $this->status = 403;
            msgr()->error('Данная операция доступна только администратору сайта.');
            app()->router()->redirect('<previous>');
            return;
        }
        $request = RequestFactory::get('article');
        if ($request->isValid()) {
            $result = Article::create($request);
        } else {
            $result = null;
        }
        if ($result) {
            msgr()->notice(t('Blog article "@name" published.', ['name' => $request->title]));
            app()->router()->redirect('<current>');
        } else {
            msgr()->warning(t('There was an error wile creating article "@name".', ['name' => $request->title]));
            app()->router()->redirect('<previous>');
        }
        exit;
    }
}
