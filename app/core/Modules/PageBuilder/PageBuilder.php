<?php

namespace Blog\Modules\PageBuilder;

use Blog\Modules\Template\Element;
use Blog\Modules\User\User;
use Symfony\Component\Yaml\Yaml;

class PageBuilder
{
    use Components\PageBuilderElements,
        Components\PageBuilderMenu;

    protected array $menu_links;
    protected array $links;
    protected array $used_tpl_id = [];
    protected array $content = [];
    protected bool $prepared = false;

    public function preparePage(): void
    {
        if ($this->prepared) {
            return;
        }
        app()->page()->setTitle(app()->controller()->getTitle());
        app()->page()->setHeader($this->header());
        app()->page()->setFooter($this->footer());
        app()->page()->useJs('js/default.min', order: 0);
        app()->page()->useJs('js/classes.min', order: 1);
        app()->page()->useJs('js/script.min', order: 2);
        $this->setAdminElement();
        $this->prepared = true;
        return;
    }

    public function getContent(string $name): array
    {
        if (!isset($this->content[$name])) {
            $name = strSuffix($name, '.yml');
            $filename = ROOTDIR . "content/{$name}";
            $this->content[$name] = file_exists($filename) ? Yaml::parseFile($filename) : [];
        }
        return $this->content[$name];
    }

    public function useId(string $id): bool
    {
        if (!IDList::instance()->use($id)) {
            msgr()->warning(
                "На странице имеется повторяющийся #id: {$id}",
                access_level: User::ACCESS_LEVEL_ADMIN
            );
            return false;
        }
        return true;
    }

    protected function setAdminElement(): void
    {
        if (!app()->user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN)) {
            return;
        }
        $bar = new Element;
        $bar->setName('elements/admin-bar');
        $bar->set('menu', app()->builder()->getMenu('admin_bar'));
        app()->library('admin-bar')->use();
        app()->page()->set('admin_bar', $bar);
        app()->page()->addClass('is-admin');
        app()->page()->useCss('/css/admin.min');
        return;
    }

    public function prepared(): bool
    {
        return $this->prepared;
    }
}
