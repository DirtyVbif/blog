<?php

namespace Blog\Controller;

class ErrorController extends BaseController
{
    public function prepare(): void
    {
        parent::prepare();
        return;
    }

    public function getTitle(): string
    {
        return t('Error 404. Page not found.');
    }

    public function postRequest(): void
    {
        pre($_POST);
        exit;
    }
}
