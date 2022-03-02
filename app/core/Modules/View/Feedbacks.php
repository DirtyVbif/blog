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
            $total_items = sql_select(from: 'comments')->count();
            if ($total_items > self::ITEMS_PER_PAGE) {
                $this->view->pager = new Pager($total_items, self::ITEMS_PER_PAGE);
            }
            $offset = $current_page * self::ITEMS_PER_PAGE;
            foreach ($this->loadData(self::ITEMS_PER_PAGE, true, $offset) as $data) {
                $feedback = new Feedback($data, Feedback::VIEW_MODE_FULL);
                $this->view->items[] = $feedback;
            }
        }
        return $this->view;
    }

    public function preview(int $limit, string $view_mode): array
    {
        $items = [];
        return $items;
    }

    /**
     * Loads comments data from storage as array
     * 
     * @return array of comments data with following keys:
     * ```
     * array('id', 'subject', 'message', 'timestamp', 'headers', 'status');
     * ```
     */
    public static function loadData(int $limit = 0, bool $order_desc = false, int $offset = 0): array
    {
        $sql = sql_select(from: Feedback::ENTITY_TABLE);
        $sql->columns(Feedback::ENTITY_COLUMNS);
        $sql->limit($limit);
        if ($offset) {
            $sql->limitOffset($offset);
        }
        $order = 'ASC';
        if ($order_desc) {
            $order = 'DESC';
        }
        $sql->order('timestamp', $order);
        return $sql->all();
    }
}
