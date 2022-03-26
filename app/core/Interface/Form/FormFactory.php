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
        $form->setClassMod('feedback');
        $form->setField('type', 'hidden')
            ->setValue('feedback');
        $form->setField('name')
            ->setAttribute('maxlength', 60)
            ->required()
            ->setLabel(t('Your name:'));
        $form->setField('email', 'email')
            ->setAttribute('maxlength', 256)
            ->required()
            ->setLabel(t('Your e-mail:'))
            ->appendDescription('Он не будет отображаться где-либо на сайте. Это только для обратной связи с Вами.');
        $form->setField('subject', 'textarea')
            ->setAttribute('rows', 6)
            ->required()
            ->setLabel(t('Message:'));
        $form->setField('submit', 'submit')
            ->setValue(t('Send'))
            ->clsI('btn btn_transparent');
        return $form;
    }
}
