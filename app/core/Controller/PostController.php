<?php

namespace Blog\Controller;

use Blog\Request\RequestFactory;

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

    public function feedbackRequest()
    {
        $request = RequestFactory::get('feedback');
        if ($request->isValid()) {
            app()->mailer()->sendFeedback($request);
            msgr()->notice('Ваше сообщение успешно отправлено и будет обработано в ближайшее время.');
        }
        return;
    }
}
