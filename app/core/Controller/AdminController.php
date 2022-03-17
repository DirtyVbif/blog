<?php

namespace Blog\Controller;

use Blog\Client\User;
use Blog\Modules\Messenger\Logger;
use Blog\Modules\TemplateFacade\Form;

class AdminController extends BaseController
{
    use Components\AdminControllerPostrequest;
    
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
        $argument = app()->router()->arg(2);
        $method = 'getRequest' . pascalCase($argument ?? '');
        if (method_exists($this, $method)) {
            return $this->$method();
        } else if (!$argument) {
            $this->viewAdminPage();
            return true;
        }
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

    protected function getRequestSkill(): bool
    {
        $argument = app()->router()->arg(3);
        if (is_numeric($argument)) {
            msgr()->warning('Complete AdminController::getRequestSkill() method for existing entity.');
            return true;
        } else if ($argument === 'create') {
            /** @var \BlogLibrary\HtmlTagsAutofill\HtmlTagsAutofill $html_tags_autofill */
            $html_tags_autofill = app()->library('html-tags-autofill');
            $html_tags_autofill->use();
            $form = new Form('skill');
            $form->tpl()->set('html_tags_autofill', $html_tags_autofill->getTemplate('form-skill--body'));
            $this->getTitle()->set('Создание нового материала типа &laquo;Навыки&raquo;');
            app()->page()->addContent($form);
            return true;
        }
        $this->status = 404;
        return false;
    }
}
