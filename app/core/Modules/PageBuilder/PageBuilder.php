<?php

namespace Blog\Modules\PageBuilder;

use Blog\Modules\Messenger\Messenger;
use Blog\Modules\Template\Element;
use Symfony\Component\Yaml\Yaml;

class PageBuilder
{
    use Components\PageBuilderElements;

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
            $this->menu_links = $this->getContent('menu-links');
        }
        $menu_items = $this->menu_links[$menu_name] ?? [];
        $links = [];
        foreach ($menu_items['items'] as $name) {            
            if ($menu_name === 'main' && $name === 'front' && app()->router()->isHome()) {
                continue;
            }
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
                if ($menu_name === 'main' && $name === 'front') {
                    $link['label'] = '';
                    $link_classes[] = $class_string . '__link_home';
                }
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
            $this->links = $this->getContent('routes');
        }
        return $this->links[$name];
    }

    protected function getContent(string $name): array
    {
        $name = strSuffix($name, '.yml');
        $filename = ROOTDIR . "content/{$name}";
        return file_exists($filename) ? Yaml::parseFile($filename) : [];
    }

    public function useId(string $id): bool
    {
        if (!IDList::instance()->use($id)) {
            msgr()->warning("На странице имеется повторяющийся #id: {$id}", Messenger::ACCESS_LEVEL_ADMIN);
            return false;
        }
        return true;
    }
}
