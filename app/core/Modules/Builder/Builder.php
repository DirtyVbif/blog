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
    protected array $links;
    protected array $used_tpl_id = [];

    public function preparePage(): void
    {
        app()->page()->setTitle(app()->controller()->getTitle());
        app()->page()->setHeader($this->header());
        app()->page()->setFooter($this->footer());
        app()->page()->useJs('js/default.min');
        app()->page()->useJs('js/classes.min');
        app()->page()->useJs('js/script.min');
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
        $menu_items = $this->menu_links[$menu_name] ?? [];
        $links = [];
        foreach ($menu_items['items'] as $name) {
            $link = $this->getLink($name);
            $link['current'] = $link['url'] === app()->router()->get('url');
            $item_classes = $link_classes = [];
            if (preg_match('/^\#/', $link['url'])) {
                $link_classes[] = 'js-anchor-link';
                if (!app()->router()->isHome()) {
                    $link['url'] = '/' . $link['url'];
                }
            }
            foreach (preg_split('/\s+/', $menu_items['class']) as $class_string) {
                $item_classes[] = $class_string . '__item';
                $link_classes[] = $class_string . '__link';
            }
            $link['class'] = implode(' ', $item_classes);
            $link['link_class'] = implode(' ', $link_classes);
            $links[$name] = $link;
        }
        return $links;
    }

    public function getLink(string $name): array
    {
        if (!isset($this->links)) {
            $this->links = $this->getSrc('links');
        }
        return $this->links[$name];
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
        $items = $this->getSrc('skills');
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

    public function useId(string $id)
    {
        if (in_array($id, $this->used_tpl_id)) {
            // TODO: notify user about html id attribute duplicate
        } else {
            array_push($this->used_tpl_id, $id);
        }
    }
}
