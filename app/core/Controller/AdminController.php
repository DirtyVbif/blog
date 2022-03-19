<?php

namespace Blog\Controller;

use Blog\Client\User;
use Blog\Modules\Entity\EntityFactory;
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
        $result = false;
        $form = new Form('skill');
        /** @var \BlogLibrary\HtmlTagsAutofill\HtmlTagsAutofill $html_tags_autofill */
        $html_tags_autofill = app()->library('html-tags-autofill');
        $html_tags_autofill->use();
        $form_data = [
            'type' => 'create',
            'action' => '/admin/skill',
            'html_tags_autofill' => $html_tags_autofill->getTemplate('form-skill--body')
        ];
        /** @var \Blog\Modules\Entity\Skill $skill */
        if (is_numeric($argument) && $skill = EntityFactory::load($argument, 'skill')) {
            $title = "Редактирование материала типа &laquo;навык&raquo; - #{$skill->id()} " . $skill->get('title');
            $form_data['title'] = $skill->get('title');
            $form_data['icon_src'] = $skill->get('icon_src');
            $form_data['icon_alt'] = $skill->get('icon_alt');
            $form_data['body'] = $skill->get('body');
            $form_data['id'] = $skill->id();
            $form_data['action'] = $skill->url();
            $form_data['type'] = 'edit';
            $result = true;
        } else if ($argument === 'create') {
            $title = 'Создание нового материала типа &laquo;навык&raquo;';
            $result = true;
        }
        if ($result) {
            $this->getTitle()->set($title);
            $form->tpl()->set($form_data);
            app()->page()->addContent($form);
        } else {
            $this->status = 404;
        }
        return $result;
    }
}
