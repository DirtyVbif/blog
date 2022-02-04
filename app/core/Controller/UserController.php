<?php

namespace Blog\Controller;

class UserController extends BaseController
{
    public function prepare(): void
    {
        parent::prepare();
        return;
    }

    public function postRequest(): void
    {
        pre($_POST);
        die;
    }

    public function getTitle(): string
    {
        return t('User\'s page');
    }
}
