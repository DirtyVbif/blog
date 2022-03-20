<?php

namespace Blog\Modules\View;

use Blog\Modules\Entity\Feedback;
use Blog\Modules\TemplateFacade\Pager;

class Feedbacks extends BaseView
{
    public const ITEMS_PER_PAGE = 100;

    protected object $view;

    public function view(): object
    {
        if (!isset($this->view)) {
            $this->view = (object)[
                'items' => [],
                'pager' => null
            ];
            $current_page = isset($_GET['page']) ? max((int)$_GET['page'], 0) : 0;
            $total_items = Feedback::count();
            if ($total_items > self::ITEMS_PER_PAGE) {
                $this->view->pager = new Pager($total_items, self::ITEMS_PER_PAGE);
            }
            $offset = $current_page * self::ITEMS_PER_PAGE;
            $this->view->items = Feedback::loadList([
                'limit' => self::ITEMS_PER_PAGE,
                'offset' => $offset,
                'order' => 'DESC',
                'view_mode' => Feedback::VIEW_MODE_FULL
            ]);
        }
        return $this->view;
    }

    public function preview(int $limit, string $view_mode): array
    {
        $items = [];
        // TODO: complete preview method for feedbacks
        return $items;
    }
}
