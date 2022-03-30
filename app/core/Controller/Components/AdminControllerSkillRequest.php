<?php

namespace Blog\Controller\Components;

use Blog\Interface\Form\FormFactory;
use Blog\Modules\Entity\EntityFactory;
use Blog\Modules\Entity\Skill;

trait AdminControllerSkillRequest
{

    protected function getRequestSkillCreate(): bool
    {
        $this->getTitle()->set('Создание нового материала типа &laquo;навык&raquo;');
        app()->page()->addContent(
            FormFactory::getSkill()
        );
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
        app()->page()->addContent(
            FormFactory::getSkill($skill)
        );
        return true;
    }
}
