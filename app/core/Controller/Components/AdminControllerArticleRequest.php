<?php

namespace Blog\Controller\Components;

use Blog\Interface\Form\FormFactory;
use Blog\Modules\Entity\Article;
use Blog\Modules\Entity\EntityFactory;

trait AdminControllerArticleRequest
{
    protected function getRequestArticleCreate(): bool
    {
        app()->controller()->getTitle()->set('Создание нового материала для блога');
        app()->page()->addContent(
            FormFactory::getArticle()
        );
        return true;
    }

    protected function getRequestArticleView(): bool
    {
        /** @var int $id current entity id */
        $id = app()->router()->arg(3);
        app()->router()->redirect(
            Article::shortLink($id)
        );
        return true;
    }

    protected function getRequestArticleEdit(): bool
    {
        /** @var int $id current entity id */
        $id = app()->router()->arg(3);
        /** @var \Blog\Modules\Entity\Article $article */
        $article = EntityFactory::load($id, 'article');
        if (!$article?->exists()) {
            return false;
        }
        app()->controller()->getTitle()->set(
            "Редактирование материала типа &laquo;статья&raquo; - #{$article->id()} " . $article->get('title')
        );
        app()->page()->addContent(
            FormFactory::getArticle($article)
        );
        return true;
    }
}
