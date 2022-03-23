<?php

namespace Blog\Controller\Components;

use Blog\Modules\Entity\Article;
use Blog\Modules\Entity\EntityFactory;
use Blog\Modules\TemplateFacade\Form;

trait AdminControllerArticleRequest
{
    protected function getRequestArticleForm(): Form
    {
        $form = new Form('article');
        $form->tpl()->useGlobals(true);
        /** @var \BlogLibrary\HtmlTagsAutofill\HtmlTagsAutofill $html_tags_autofill */
        $html_tags_autofill = app()->library('html-tags-autofill');
        $html_tags_autofill->use();
        $form->tpl()->set('html_tags_autofill', $html_tags_autofill->getTemplate('form-skill--body'));
        return $form;
    }

    protected function getRequestArticleCreate(): bool
    {
        app()->controller()->getTitle()->set('Создание нового материала для блога');
        $form = $this->getRequestArticleForm();
        $form->tpl()->set('type', 'create');
        $form->tpl()->set('action', '/admin/article');
        app()->page()->addContent($form);
        return true;
    }

    protected function getRequestArticleView(): bool
    {
        /** @var int $id current entity id */
        $id = app()->router()->arg(3);
        app()->router()->redirect(Article::shortLink($id));
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
        $form = $this->getRequestArticleForm();
        $form->tpl()->set('article', $article);
        $form->tpl()->set('form_action', '/admin/article/' . $article->id());
        $form->tpl()->set('form_type', 'edit');
        app()->page()->addContent($form);
        return true;
    }
}
