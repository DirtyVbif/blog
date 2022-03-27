<?php

namespace Blog\Interface\Form;

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

    public static function getFeedback(): Form
    {
        $form = new Form('feedback');
        $form->setMethod('post');
        $form->useCsrf();
        $form->setField('type', 'hidden')
            ->setValue('feedback');
        $form->setField('name')
            ->setAttribute('maxlength', 60)
            ->required()
            ->setLabel(t('Your name:'))
            ->inlineLabel(true);
        $form->setField('email', 'email')
            ->setAttribute('maxlength', 256)
            ->required()
            ->setLabel(t('Your e-mail:'))
            ->inlineLabel(true)
            ->appendDescription('Он не будет отображаться где-либо на сайте. Это только для обратной связи с Вами.');
        $form->setField('subject', 'textarea')
            ->setAttribute('rows', 6)
            ->required()
            ->setLabel(t('Message:'));
        $form->setSubmit()
            ->setValue(t('Send'))
            ->clsI('btn btn_transparent');
        return $form;
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
            ->setLabel(t('Login:'))
            ->setAttribute('maxlength', 256)
            ->inlineLabel(true)
            ->required();
        $form->setField('password', 'password', section: 'body')
            ->setLabel(t('Password:'))
            ->setAttribute('minlength', 8)
            ->setAttribute('maxlength', 64)
            ->inlineLabel(true)
            ->required();
        $form->setField('remember_me', 'checkbox', section: 'footer')
            ->setLabel(t('Remember me'))
            ->setAttribute('checked')
            ->setValue(1)
            ->setOrder(FormField::ORDER_BEFORE_IN_LABEL);
        $form->setSubmit(section: 'footer')
            ->setValue(t('Sign in'))
            ->clsI('btn btn_transparent');
        return $form;
    }
}
