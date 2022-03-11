<?php

namespace Blog\Controller;

use Blog\Client\User;
use Blog\Modules\Messenger\Logger;

class AdminController extends BaseController
{
    protected int $status = 200;

    public function prepare(): void
    {
        parent::prepare();
        if (!$this->validateRequest()) {
            // if access denied
            /** @var ErrorController $err_c */
            $conerr = app()->controller('error');
            $conerr->prepare($this->status);
            return;
        }
        return;
    }

    protected function validateRequest(): bool
    {
        if (!user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN)) {
            $this->status = 403;
            return false;
        }
        $argument = app()->router()->arg(2);
        $method = 'getRequest' . pascalCase($argument ?? '');
        if (method_exists($this, $method)) {
            return $this->$method();
        } else if (!$argument) {
            $this->viewAdminPage();
            return true;
        }
        // TODO: validate request for correct request
        $this->status = 404;
        return false;
    }

    protected function viewAdminPage(): void
    {
        $this->getTitle()->set('Administrative page');
        return;
    }

    protected function getRequestLog(): bool
    {
        $log = Logger::getSystemLog();
        // TODO: complete system log output view
        msgr()->debug($log);
        return true;
    }

    public function postRequest(): void
    {
        pre($_POST);
        exit;
    }
}
