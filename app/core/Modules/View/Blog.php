<?php

namespace Blog\Modules\View;

use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\TemplateFacade\BlogArticle;
use Blog\Modules\TemplateFacade\Pager;
use Blog\Modules\View\BaseView;

class Blog extends BaseView
{
    protected const ITEMS_PER_PAGE = 12;

    public function getArticles(int $limit = 0, bool $order_desc = false, int $offset = 0): array
    {
        $sql = sql_select(
            ['id', 'title', 'summary', 'body', 'created', 'updated', 'status', 'alias', 'preview_src', 'preview_alt'],
            'articles'
        );
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

    public function getArticleById(int $id)
    {
        $sql = sql_select(
            ['id', 'title', 'summary', 'body', 'created', 'updated', 'status', 'alias', 'preview_src', 'preview_alt'],
            'articles'
        );
        $sql->where(condition: ['id' => $id]);
        return $sql->first();
    }
    
    /**
     * @return BlogArticle[] $items
     */
    public function preview(int $limit, string $view_format = 'teaser')
    {
        $items = $this->getArticlesItemsFromData(
            $this->getArticles($limit, true),
            $view_format
        );
        return $items;
    }

    /**
     * @return BlogArticle[] $items
     */
    protected function getArticlesItemsFromData(array $articles, string $view_format)
    {
        $items = [];
        foreach ($articles as $data) {
            $data['url'] = '/blog/' . ($data['alias'] ?? $data['id']);
            $data['date'] = new DateFormat($data['created']);
            $items[] = new BlogArticle($data, $view_format);
        }
        return $items;
    }
    
    public function view(): object
    {
        $view = (object)[
            'items' => [],
            'pager' => null
        ];
        // TODO: complete view output with pager
        $current_page = isset($_GET['page']) ? max((int)$_GET['page'], 0) : 0;
        $total_items = sql_select(from: 'articles')->count();
        if ($total_items > self::ITEMS_PER_PAGE) {
            $view->pager = new Pager($total_items, self::ITEMS_PER_PAGE);
        }
        $offset = $current_page * self::ITEMS_PER_PAGE;
        $view->items = $this->getArticlesItemsFromData(
            $this->getArticles(self::ITEMS_PER_PAGE, true, $offset),
            'preview'
        );
        return $view;
    }
}
