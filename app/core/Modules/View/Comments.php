<?php

namespace Blog\Modules\View;

use Blog\Modules\Entity\CommentPrototype;
use Blog\Modules\TemplateFacade\Pager;
use Blog\Modules\User\User;

class Comments extends BaseView
{
    public const ITEMS_PER_PAGE = 20;

    public static function viewCommentsPage(): void
    {
        app()->page()->addContent(
            app()->builder()->getBlogCommentsPage()
        );
        app()->page()->content()->addClass('container_comments');
        return;
    }

    /**
     * @return CommentPrototype[] $items
     */
    public function preview(int $limit, string $view_format = CommentPrototype::VIEW_MODE_FULL): array
    {
        $items = [];
        // TODO: complete preview for last blog comments
        return $items;
    }
    
    public function view(): object
    {
        $view = (object)[
            'items' => [],
            'pager' => null
        ];
        $current_page = isset($_GET['page']) ? max((int)$_GET['page'], 0) : 0;
        $sql = sql_select(from: ['c' => 'comments']);
        $sql->columns(['c' => ['cid']]);
        $sql->join(['ec' => 'entities_comments'], using: 'cid');
        $sql->where(['ec.deleted' => 0]);
        if (!user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN)) {
            $sql->andWhere(['c.status' => 1]);
        }
        $result = $sql->all();
        $total_items = count($result);
        if ($total_items > self::ITEMS_PER_PAGE) {
            $view->pager = new Pager($total_items, self::ITEMS_PER_PAGE);
        }
        $offset = $current_page * self::ITEMS_PER_PAGE;
        $view->items = CommentPrototype::loadList([
            'limit' => self::ITEMS_PER_PAGE,
            'offset' => $offset,
            'order' => 'DESC',
            'view_mode' => CommentPrototype::VIEW_MODE_FULL
        ]);
        return $view;
    }
}
