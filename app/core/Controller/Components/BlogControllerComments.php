<?php

namespace Blog\Controller\Components;

use Blog\Modules\Entity\Comment;
use Blog\Modules\User\User;
use Blog\Modules\View\Comments;

trait BlogControllerComments
{
    protected function getRequestComment(): bool
    {
        // verify user access level
        if (!app()->user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN)) {
            $this->status = 403;
            return false;
        }
        // check requested comment id and action
        $cid = app()->router()->arg(3);
        $action = app()->router()->arg(4);
        if (!$cid || !$action) {
            $this->status = 404;
            return false;
        }
        $action = pascalCase($action);
        if (
            !method_exists(Comment::class, $action)
            || !Comment::$action($cid)
        ) {
            $this->status = 404;
            return false;
        }
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
        /** @var \Blog\Controller\BaseController $this */
        $this->getTitle()->set('Комментарии пользователей в блоге');
        Comments::viewCommentsPage();
        return true;
    }
}