<?php

namespace Blog\Modules\PageBuilder\Components;

use Blog\Modules\Template\Element;
use Blog\Modules\Template\PageFooter;
use Blog\Modules\Template\PageHeader;
use Blog\Modules\TemplateFacade\BodyText;
use Blog\Modules\TemplateFacade\Image;
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

    public function getBanner(): Element
    {
        $slider = new Element('section');
        $slider->setName('elements/banner');
        $slider->addClass('section section_banner');
        return $slider;
    }

    public function getSkills(): Element
    {
        $skills = new Element('section');
        $skills->setName('elements/skills');
        $skills->addClass('section section_skills')
            ->setId('about');
        $items = $this->getContent('skills');
        foreach ($items as &$item) {
            $item['icon'] = new Image($item['icon']);
            $item['icon']->width(120);
            $item['desc'] = new BodyText($item['desc']);
        }
        $skills->set('items', $items);
        $label = new Title(2);
        $label->set(t('My skills'));
        $label->addClass('section__header section_skills__header');
        $skills->set('label', $label);
        return $skills;
    }

    public function getSummary(): Element
    {
        $summary = new Element('section');
        $summary->setName('elements/summary');
        $summary->addClass('section section_summary')
            ->setId('summary');
        $label = new Title(2);
        $label->set(t('Web-developer summary'));
        $label->addClass('section__header section_summary__header');
        $diff = date_diff(
            date_create('26-08-1989'),
            date_create(date("Y-m-d"))
        );
        $summary->set('label', $label)
            ->set('my_age', $diff->format('%y'));
        return $summary;
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
