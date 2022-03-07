<?php

namespace Blog\Modules\PageBuilder\Components;

use Blog\Modules\Template\Element;

trait PageBuilderMenu
{
    public function getMenu(string $name, array $arguments = []): ?Element
    {
        /** @var array $links */
        $menu_data = $this->getMenuData($name);
        if (empty($menu_data)) {
            return null;
        }
        $menu_data['class'] ??= "menu menu_{$name}";
        $links = $this->buildMenuLinks($menu_data, $arguments);
        if (empty($links)) {
            return null;
        }
        $menu = new Element('ul');
        $menu->setName('elements/menu')
            ->set('items', $links);
        $menu->addClass($menu_data['class']);
        $menu->setId(kebabCase("menu {$name}"));
        return $menu;
    }

    protected function getMenuData(string $menu_name): array
    {
        if (!isset($this->menu_links)) {
            $this->menu_links = app()->builder()->getContent('menu-links');
        }
        return $this->menu_links[$menu_name] ?? [];
    }

    protected function buildMenuLinks(array $menu_data, array $arguments = []): array
    {
        $links = [];
        if (
            isset($menu_data['access_level'])
            && !app()->user()->verifyAccessLevel($menu_data['access_level'])
        ) {
            return $links;
        }
        foreach ($menu_data['items'] ?? [] as $name) {
            $link = $this->getLink($name);
            if (
                !$link
                || (
                    isset($link['access_level'])
                    && !app()->user()->verifyAccessLevel($link['access_level'])
                )
            ) {
                continue;
            }
            foreach ($arguments as $arg => $value) {
                $link['url'] = str_replace('{$' . $arg . '}', $value, $link['url']);
            }
            $link['current'] = $this->isLinkMatchsCurrentUrl($link['url']);
            if (preg_match('/^\#/', $link['url'])) {
                $link_classes[] = 'js-anchor-link';
                if (!app()->router()->isHome()) {
                    $link['url'] = '/' . $link['url'];
                }
            }
            $link['class'] = classlistToString($menu_data['class'], suffix: '__item');
            $link['link_class'] = classlistToString($menu_data['class'], suffix: '__link');
            $links[$name] = $link;
        }
        return $links;
    }

    public function getLink(string $name): ?array
    {
        if (!isset($this->links)) {
            $this->links = app()->builder()->getContent('routes');
        }
        return $this->links[$name] ?? null;
    }

    protected function isLinkMatchsCurrentUrl(string $url): bool
    {
        $parts = explode('?', $url);
        $args = explode('/', $parts[0]);
        $result = true;
        $i = 1;
        foreach ($args as $arg) {
            if (!$arg) {
                continue;
            } else if ($arg !== app()->router()->arg($i)) {
                $result = false;
            }
            $i++;
        }
        return $result;
    }
}
