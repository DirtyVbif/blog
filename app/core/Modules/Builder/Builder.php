<?php

namespace Blog\Modules\Builder;

use Blog\Modules\Template\Element;
use Blog\Modules\Template\PageHeader;
use Symfony\Component\Yaml\Yaml;

class Builder
{
    protected PageHeader $page_header;
    protected Element $logo;
    protected array $menu_links;

    public function preparePage(): void
    {
        app()->page()->setTitle(app()->controller()->getTitle());
        app()->page()->setHeader($this->header());
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
            $this->page_header->set('logo', $this->getLogo());
        }
        return $this->page_header;
    }

    public function getLogo(): Element
    {
        if (!isset($this->logo)) {
            $this->logo = new Element('a');
            $this->logo->setName('elements/logo');
            $this->logo->setAttr('href', '/')
                ->setAttr('title', 'Go home page');
        }
        return $this->logo;
    }
}
