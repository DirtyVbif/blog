<?php

namespace Blog\Controller\Components;

use Blog\Modules\Entity\Comment;
use Blog\Request\CommentRequest;

trait BlogControllerComments
{

    protected function postRequestCommentAdd(array $data): void
    {
        $request = new CommentRequest($data);
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
        $redirect = ($data['article_id'] ?? false) ? '/blog/' . $data['article_id'] : '<current>';
        app()->router()->redirect($redirect);
        return;
    }

    protected function getRequestComment(): bool
    {
        // check requested comment id and action
        $cid = app()->router()->arg(3);
        $action = app()->router()->arg(4);
        if (!$cid || !$action) {
            $this->status = 404;
            return false;
        }
        // verify user access level
        if (!app()->user()->verifyAccessLevel(4)) {
            $this->status = 403;
            return false;
        }
        $action = pascalCase($action);
        $comment = new Comment($cid);
        if (!$comment->exists() || !method_exists($comment, $action)) {
            $this->status = 404;
            return false;
        }
        // comment delete or approve actions
        $comment->$action();
        app()->router()->redirect('<previous>');
        return true;
    }

    protected function getRequestComments(): bool
    {
        // set page meta
        app()->page()->setMetaTitle(stok('Комментарии к блогу | :[site]'));
        app()->page()->setMeta('description', [
            'name' => 'description',
            'content' => 'Обзор комментариев пользователей в блоге веб-разработчика'
        ]);
        app()->controller()->getTitle()->set('Комментарии пользователей в блоге');
        return true;
    }
}