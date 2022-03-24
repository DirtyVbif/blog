<?php

namespace Blog\Controller\Components;

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
            'error' => 'Unknown request type for ' . static::class . '::postRequest()',
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
        app()->router()->redirect('<previous>');
        return;
    }
}
