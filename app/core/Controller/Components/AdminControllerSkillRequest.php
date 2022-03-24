<?php

namespace Blog\Controller\Components;

use Blog\Modules\Entity\EntityFactory;
use Blog\Modules\Entity\Skill;
use Blog\Modules\TemplateFacade\Form;

trait AdminControllerSkillRequest
{
    protected function getRequestSkillForm(): Form
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
        $form = $this->getRequestSkillForm();
        $form->tpl()->set('type', 'create');
        $form->tpl()->set('action', '/admin/skill');
        app()->page()->addContent($form);
        return true;
    }

    protected function getRequestSkillView(): bool
    {
        /** @var int $id current entity id */
        $id = app()->router()->arg(3);
        /** @var \Blog\Modules\Entity\Skill $skill */
        $skill = EntityFactory::load($id, 'skill');
        if (!$skill?->exists()) {
            return false;
        }
        $skill->setViewMode(Skill::VIEW_MODE_FULL);
        app()->controller()->getTitle()->set(
            "View of entity #{$skill->id()} &laquo;" . $skill->get('title') . '&raquo;'
        );
        app()->page()->addContent($skill);
        app()->page()->useCss('css/skills.min');
        return true;
    }

    protected function getRequestSkillEdit(): bool
    {
        /** @var int $id current entity id */
        $id = app()->router()->arg(3);
        /** @var \Blog\Modules\Entity\Skill $skill */
        $skill = EntityFactory::load($id, 'skill');
        if (!$skill?->exists()) {
            return false;
        }
        app()->controller()->getTitle()->set(
            "Редактирование материала типа &laquo;навык&raquo; - #{$skill->id()} " . $skill->get('title')
        );
        $form = $this->getRequestSkillForm();
        $form->tpl()->set('title', $skill->get('title'));
        $form->tpl()->set('icon_src', $skill->get('icon_src'));
        $form->tpl()->set('icon_alt', $skill->get('icon_alt'));
        $form->tpl()->set('body', $skill->get('body'));
        $form->tpl()->set('status', $skill->get('status'));
        $form->tpl()->set('id', $skill->id());
        $form->tpl()->set('action', $skill->url());
        $form->tpl()->set('type', 'edit');
        app()->page()->addContent($form);
        return true;
    }
}
