<?php

namespace Blog\Controller;

class ErrorController extends BaseController
{
    public function prepare(int $status = 404): void
    {
        parent::prepare();
        // TODO: parse status for specific errors output
        app()->page()->setTitle($this->getTitle());
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
