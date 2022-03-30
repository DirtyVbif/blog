<?php

namespace Blog\Interface\Form;

use Blog\Modules\Entity\Article;
use Blog\Modules\Entity\Comment;
use Blog\Modules\Entity\Feedback;
use Blog\Modules\Entity\Skill;
use BlogLibrary\HtmlTagsAutofill\HtmlTagsAutofill;
use com_exception;

class FormFactory
{
    public static function get(string $form_name): ?Form
    {
        $method = camelCase("get {$form_name}");
        if (!method_exists(static::class, $method)) {
            return null;
        }
        return self::{$method}();
    }

    public static function getLogin(): Form
    {
        $form = new Form('login');
        $form->setMethod('post');
        $form->setAction('/user');
        $form->useCsrf();
        $form->setField('type', 'hidden')
            ->setValue('login');
        $form->setSection('body');
        $form->setSection('footer');
        $form->setField('mail', 'email', section: 'body')
            ->setLabel(t('Login') . ':')
            ->inlineLabel(true)
            ->required()
            ->setAttribute('maxlength', 256);
        $form->setField('password', 'password', section: 'body')
            ->setLabel(t('Password') . ':')
            ->inlineLabel(true)
            ->required()
            ->setAttribute('minlength', 8)
            ->setAttribute('maxlength', 64);
        $form->setField('remember_me', 'checkbox', section: 'footer')
            ->setLabel(t('Remember me'))
            ->setValue(1)
            ->setOrder(FormField::ORDER_BEFORE_IN_LABEL)
            ->setAttribute('checked');
        $form->setSubmit(section: 'footer')
            ->setValue(t('Sign in'))
            ->addClass('btn btn_transparent');
        return $form;
    }

    public static function getFeedback(): Form
    {
        return Feedback::getForm();
    }

    public static function getComment(int $entity_id, int $parent_id = 0): Form
    {
        return Comment::getForm($entity_id, parent_id: $parent_id);
    }

    public static function getArticle(?Article $entity = null): Form
    {
        $form = Article::getForm($entity);
        $form->f('body')->appendDescription(
            HtmlTagsAutofill::get('form-entity--body')
        );
        return $form;
    }

    public static function getSkill(?Skill $entity = null): Form
    {
        $form = Skill::getForm($entity);
        $form->f('body')->appendDescription(
            HtmlTagsAutofill::get('form-entity--body')
        );
        return $form;
    }
}
