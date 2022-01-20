<?php

namespace Blog\Modules\PageBuilder\Components;

use Blog\Modules\Template\Element;
use Blog\Modules\Template\PageFooter;
use Blog\Modules\Template\PageHeader;
use Blog\Modules\TemplateFacade\Title;

trait PageBuilderElements
{
    protected PageHeader $page_header;
    protected PageFooter $page_footer;

    public function header(): PageHeader
    {
        if (!isset($this->page_header)) {
            $this->page_header = new PageHeader;
            $logo = $this->getLogo();
            $logo->setAttr('class', 'logo logo_header');
            $this->page_header->set('logo', $logo);
        }
        return $this->page_header;
    }

    public function footer(): PageFooter
    {
        if (!isset($this->page_footer)) {
            $this->page_footer = new PageFooter;
            $logo = $this->getLogo();
            $logo->addClass('logo_footer');
            $this->page_footer->set('logo', $logo);
            $this->page_footer->set('copyrights', $this->getCopyrights());
        }
        return $this->page_footer;
    }

    public function getLogo(): Element
    {
        $logo = new Element('a');
        $logo->setName('elements/logo');
        $logo->setAttr('href', '/')
            ->setAttr('title', t('Go home page'))
            ->setAttr('class', 'logo');
        return $logo;
    }

    public function getCopyrights(): Element
    {
        $cp = new Element;
        $cp->setName('elements/copyrights');
        $cp->setAttr('class', 'copyrights');
        $cp->set('current_year', date('Y'));
        return $cp;
    }

    public function getSlider(): Element
    {
        $slider = new Element('section');
        $slider->setName('elements/slider');
        $slider->addClass('slider');
        return $slider;
    }

    public function getSkills(): Element
    {
        $skills = new Element('section');
        $skills->setName('elements/skills');
        $skills->addClass('skills');
        $items = $this->getContent('skills');
        $skills->set('items', $items);
        $label = new Title(2);
        $label->set(t('My skills'));
        $skills->set('label', $label);
        return $skills;
    }

    public function getCookieModal(): Element
    {
        $chunk = new Element;
        $chunk->setName('elements/accept-cookies');
        $chunk->addClass('cookie-agreement hidden');
        $chunk->setId('cookie-agreement');
        return $chunk;
    }
}
