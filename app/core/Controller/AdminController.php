<?php

namespace Blog\Controller;

use Blog\Client\User;
use Blog\Modules\Entity\EntityFactory;
use Blog\Modules\Entity\Skill;
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
        if ($argument === 'create') {
            $result = $this->getRequestSkillCreate();
        } else if (is_numeric($argument)) {
            $action = app()->router()->arg(4) ?? 'view';
            $method = 'get' . pascalCase('request skill' . $action);
            $result = method_exists($this, $method) ? $this->{$method}($argument) : false;
        }
        if (!$result) {
            $this->status = 404;
        }
        return $result;
    }

    protected function getRequestSkillFrom(): Form
    {
        $form = new Form('skill');
        /** @var \BlogLibrary\HtmlTagsAutofill\HtmlTagsAutofill $html_tags_autofill */
        $html_tags_autofill = app()->library('html-tags-autofill');
        $html_tags_autofill->use();
        $form->tpl()->set('html_tags_autofill', $html_tags_autofill->getTemplate('form-skill--body'));
        return $form;
    }

    protected function getRequestSkillCreate(): bool
    {
        $this->getTitle()->set('Создание нового материала типа &laquo;навык&raquo;');
        $form = $this->getRequestSkillFrom();
        $form->tpl()->set('type', 'create');
        $form->tpl()->set('action', '/admin/skill');
        app()->page()->addContent($form);
        return true;
    }

    protected function getRequestSkillView(int $id): bool
    {
        /** @var \Blog\Modules\Entity\Skill $skill */
        $skill = EntityFactory::load($id, 'skill');
        if (!$skill?->exists()) {
            return false;
        }
        $skill->setViewMode(Skill::VIEW_MODE_FULL);
        $this->getTitle()->set(
            "View of entity #{$skill->id()} &laquo;" . $skill->get('title') . '&raquo;'
        );
        app()->page()->addContent($skill);
        app()->page()->useCss('css/skills.min');
        return true;
    }

    protected function getRequestSkillEdit(int $id): bool
    {
        /** @var \Blog\Modules\Entity\Skill $skill */
        $skill = EntityFactory::load($id, 'skill');
        if (!$skill?->exists()) {
            return false;
        }
        $this->getTitle()->set(
            "Редактирование материала типа &laquo;навык&raquo; - #{$skill->id()} " . $skill->get('title')
        );
        $form = $this->getRequestSkillFrom();
        $form->tpl()->set('type', 'create');
        $form->tpl()->set('action', '/admin/skill');
        $form->tpl()->set('title', $skill->get('title'));
        $form->tpl()->set('icon_src', $skill->get('icon_src'));
        $form->tpl()->set('icon_alt', $skill->get('icon_alt'));
        $form->tpl()->set('body', $skill->get('body'));
        $form->tpl()->set('id', $skill->id());
        $form->tpl()->set('action', $skill->url());
        $form->tpl()->set('type', 'edit');
        app()->page()->addContent($form);
        return true;
    }

    protected function getRequestSkillDelete(int $id): bool
    {
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
