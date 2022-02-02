<?php

namespace Blog\Controller;

class PostController extends BaseController
{
    public function prepare(): void
    {
        $type = $_POST['type'] ?? null;
        if ($type && method_exists($this, $method = lcfirst(strPascalCase($type)) . 'Request')) {
            $this->$method();
        } else {
            msgr()->warning('POST request is not valid.');
        }
        die;
        app()->router()->redirect('<current>');
    }

    public function getTitle(): string
    {
        return t('Error 404. Page not found.');
    }

    public function feedbackRequest()
    {
        pre($_POST);
    }
}
