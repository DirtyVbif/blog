<?php

namespace Blog\Modules\View;

use Blog\Modules\Entity\BlogArticle;
use Blog\Modules\Entity\Comment;
use Blog\Modules\TemplateFacade\Pager;
use Blog\Modules\User\User;

class BlogComments extends BaseView
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
     * @return Comment[] $items
     */
    public function preview(int $limit, string $view_format = Comment::VIEW_MODE_FULL): array
    {
        $items = [];
        // TODO: complete preview for last blog comments
        // foreach ($this->loadArticlesData($limit, true) as $data) {
        //     $items[] = new BlogArticle($data, $view_format);
        // }
        return $items;
    }
    
    public function view(): object
    {
        $view = (object)[
            'items' => [],
            'pager' => null
        ];
        $current_page = isset($_GET['page']) ? max((int)$_GET['page'], 0) : 0;
        $total_items = sql_select(from: 'comments')->count();
        if ($total_items > self::ITEMS_PER_PAGE) {
            $view->pager = new Pager($total_items, self::ITEMS_PER_PAGE);
        }
        $offset = $current_page * self::ITEMS_PER_PAGE;
        foreach ($this->loadData(self::ITEMS_PER_PAGE, true, $offset) as $data) {
            $comment = new Comment($data, Comment::VIEW_MODE_FULL);
            $comment->tpl()->set(
                'url',
                BlogArticle::generateUrl($data['alias'], $data['aid']) . "#comment-{$comment->id()}"
            );
            $view->items[] = $comment;
        }
        return $view;
    }

    /**
     * Loads comments data from storage as array
     * 
     * @return array of articles data with following keys:
     * * `array('id', 'title', 'summary', 'body', 'created', 'updated', 'status', 'alias', 'preview_src', 'preview_alt')`
     */
    public static function loadData(int $limit = 0, bool $order_desc = false, int $offset = 0): array
    {
        $columns = [
            'c' => ['cid', 'pid', 'created', 'name', 'email', 'body', 'status', 'ip'],
            'ac' => ['aid'],
            'a' => ['title', 'alias']
        ];
        $sql = sql_select(from: ['c' => 'comments']);
        $sql->join(['ac' => 'article_comments'], using: 'cid');
        $sql->join(['a' => 'articles'], on: ['ac.aid', 'a.id']);
        $sql->columns($columns);
        $sql->where(['ac.deleted' => 0]);
        if (!app()->user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN)) {
            $sql->andWhere(['c.status' => 1]);
        }
        $sql->limit($limit);
        if ($offset) {
            $sql->limitOffset($offset);
        }
        $order = 'ASC';
        if ($order_desc) {
            $order = 'DESC';
        }
        $sql->order('created', $order);
        return $sql->all();
    }
}
