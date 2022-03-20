<?php

namespace Blog\Modules\View;

use Blog\Modules\Entity\Skill;

class Skills extends BaseView
{
    public const ITEMS_PER_PAGE = 0;

    /**
     * @return Skill[] $items
     */
    public function preview(int $limit, string $view_format = Skill::VIEW_MODE_TEASER): array
    {
        $items = Skill::loadList([
            'view_mode' => $view_format,
            'limit' => $limit
        ]);
        return $items;
    }

    public function view(): object
    {
        $view = (object)[];
        $view->items = Skill::loadList([
            'view_mode' => Skill::VIEW_MODE_FULL
        ]);
        return $view;
    }
}
