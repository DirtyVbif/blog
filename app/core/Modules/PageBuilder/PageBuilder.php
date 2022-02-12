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

    public function preparePage(): void
    {
        app()->page()->setTitle(app()->controller()->getTitle());
        app()->page()->setHeader($this->header());
        app()->page()->setFooter($this->footer());
        app()->page()->useJs('js/default.min');
        app()->page()->useJs('js/classes.min');
        app()->page()->useJs('js/script.min');
        $this->setAdminElement();
        return;
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
        app()->page()->set('admin_bar', $bar);
        app()->page()->addClass('is-admin');
        app()->page()->useCss('admin.min');
        return;
    }
}
