<?php

namespace Blog\Modules\Builder;

use Blog\Modules\Template\Element;
use Blog\Modules\Template\PageFooter;
use Blog\Modules\Template\PageHeader;
use Blog\Modules\TemplateFacade\Title;
use Symfony\Component\Yaml\Yaml;

class Builder
{
    protected PageHeader $page_header;
    protected PageFooter $page_footer;
    protected array $menu_links;

    public function preparePage(): void
    {
        app()->page()->setTitle(app()->controller()->getTitle());
        app()->page()->setHeader($this->header());
        app()->page()->setFooter($this->footer());
        return;
    }

    public function getMenu(string $name): Element
    {
        /** @var array $links */
        $links = $this->getMenuLinks($name);
        $menu = new Element('ul');
        $menu->setName('elements/menu')
            ->set('items', $links);
        $menu->setAttr('class', "menu menu_{$name}");
        return $menu;
    }

    public function getMenuLinks(string $menu_name): array
    {
        if (!isset($this->menu_links)) {
            $this->menu_links = $this->getSrc('menu-links');
        }
        $links = $this->menu_links[$menu_name] ?? [];
        foreach ($links as &$link) {
            $link['current'] = $link['url'] === app()->router()->get('url');
            if (preg_match('/^\#/', $link['url']) && !app()->router()->isHome()) {
                $link['url'] = '/' . $link['url'];
            }
        }
        return $links;
    }

    protected function getSrc(string $name): array
    {
        $name = strSuffix($name, '.yml');
        $filename = COREDIR . "Modules/Builder/src/{$name}";
        return file_exists($filename) ? Yaml::parseFile($filename) : [];
    }

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
            $logo->setAttr('class', 'logo logo_footer');
            $this->page_footer->set('logo', $logo);
        }
        return $this->page_footer;
    }

    public function getLogo(): Element
    {
        $logo = new Element('a');
        $logo->setName('elements/logo');
        $logo->setAttr('href', '/')
            ->setAttr('title', t('Go home page'));
        return $logo;
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
        $items = $this->getSrc('skills');
        $skills->set('items', $items);
        $label = new Title(2);
        $label->set(t('My skills'));
        $skills->set('label', $label);
        return $skills;
    }
}
