<?php

namespace Blog\Controller;

use Blog\Client\User;
use Blog\Modules\Entity\EntityFactory;
use Blog\Modules\Messenger\Logger;

class AdminController extends BaseController
{
    use Components\AdminControllerPostRequest,
        Components\AdminControllerArticleRequest,
        Components\AdminControllerSkillRequest;
    
    public const ADMIN_ACCESS_LEVEL = User::ACCESS_LEVEL_ADMIN;

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
        if (!user()->verifyAccessLevel(self::ADMIN_ACCESS_LEVEL)) {
            $this->status = 403;
            return false;
        }
        $type = app()->router()->arg(2);
        $argument = app()->router()->arg(3);
        $action = app()->router()->arg(4);
        $method = null;
        $result = false;
        if (!$type) {
            $this->viewAdminPage();
            return true;
        } else if ($argument && !is_numeric($argument)) {
            $method = 'get' . pascalCase("request {$type} {$argument}");
        } else if ($argument) {
            $action ??= 'view';
            $method = 'get' . pascalCase("request {$type} {$action}");
        } else if ($type) {
            $method = 'get' . pascalCase("request {$type}");
        }
        if ($method && method_exists($this, $method)) {
            $result = $this->{$method}();
        } else if ($action === 'delete') {
            $result = $this->getRequestEntityDelete();
        }
        $this->status = $result ? $this->status : 404;
        return $result;
    }

    protected function viewAdminPage(): void
    {
        msgr()->warning('View for main administrative page is incomplete.');
        $this->getTitle()->set('Administrative page');
        // TODO: complete view for main administrative page
        return;
    }

    protected function getRequestLog(): bool
    {
        $log = Logger::getSystemLog();
        // TODO: complete system log output view
        msgr()->debug($log);
        return true;
    }

    protected function getRequestEntityDelete(): bool
    {
        /** @var int $id current entity id */
        $id = app()->router()->arg(3);
        $result = EntityFactory::delete($id);
        if ($result) {
            $method = 'notice';
            $message = 'Entity #@id of type &laquo;@type&raquo; successfully deleted.';
        } else {
            $method = 'error';
            $message = 'Entity #@id of type &laquo;@type&raquo; wasn\'t deleted.';
        }
        msgr()->{$method}(
            t($message, ['id' => $id, 'type' => 'skill'])
        );
        app()->router()->redirect('<previous>');
        return true;
    }
}
