<?php

namespace Blog\Modules\Builder;

use Blog\Modules\Template\Element;
use Blog\Modules\Template\PageFooter;
use Blog\Modules\Template\PageHeader;
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
            $this->menu_links = Yaml::parseFile(__DIR__ . '/src/menu-links.yml');
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
        $slider->setAttr('class', 'slider');
        return $slider;
    }
}
