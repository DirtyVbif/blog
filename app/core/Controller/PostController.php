<?php

namespace Blog\Controller;

use Blog\Request\FeedbackRequest;

class PostController extends BaseController
{
    public function prepare(): void
    {
        $type = $_POST['type'] ?? null;
        $controller = app()->router()->arg(1);
        if (!$controller && $type && method_exists($this, $method = lcfirst(pascalCase($type)) . 'Request')) {
            $this->$method();
        } else if ($controller) {
            app()->controller($controller)->postRequest();
            return;
        } else {
            msgr()->warning('POST request is not valid.');
        }
        app()->router()->redirect('<current>');
    }

    public function postRequest(): void
    {
        $this->prepare();
        return;
    }

    public function getTitle(): string
    {
        return '';
    }

    public function feedbackRequest()
    {
        $data = new FeedbackRequest($_POST);
        if ($data->isValid()) {
            $data->sendAsMail();
            msgr()->notice('Ваше сообщение успешно отправлено и будет обработано в ближайшее время.');
        }
        return;
    }
}
