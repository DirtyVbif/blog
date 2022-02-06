<?php

namespace Blog\Modules\View;

use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\TemplateFacade\BlogArticle;
use Blog\Modules\TemplateFacade\Pager;
use Blog\Modules\View\BaseView;

class Blog extends BaseView
{
    protected const ITEMS_PER_PAGE = 12;

    public function loadArticlesData(int $limit = 0, bool $order_desc = false, int $offset = 0): array
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

    public function getArticleById(int $id): ?BlogArticle
    {
        $data = $this->loadArticleDataByColumn('id', $id);
        if (empty($data)) {
            return null;
        }
        $article = $this->getArticleFromData($data);
        return $article;
    }

    public function getArticleByAlias(string $alias): ?BlogArticle
    {
        $data = $this->loadArticleDataByColumn('alias', $alias);
        if (empty($data)) {
            return null;
        }
        $article = $this->getArticleFromData($data);
        return $article;
    }

    protected function loadArticleDataByColumn(string $column, string $search_value): array
    {
        $sql = sql_select(
            ['id', 'title', 'summary', 'body', 'created', 'updated', 'status', 'alias', 'preview_src', 'preview_alt', 'author'],
            'articles'
        );
        $sql->where(condition: [$column => $search_value]);
        return $sql->first();
    }
    
    /**
     * @return BlogArticle[] $items
     */
    public function preview(int $limit, string $view_format = BlogArticle::VIEW_MODE_TEASER)
    {
        $items = [];
        foreach ($this->loadArticlesData($limit, true) as $data) {
            $items[] = $this->getArticleFromData($data, $view_format);
        }
        return $items;
    }

    protected function getArticleFromData(array $data, string $view_format = BlogArticle::VIEW_MODE_FULL): BlogArticle
    {
        $data['url'] = '/blog/' . ($data['alias'] ?? $data['id']);
        $data['date'] = new DateFormat($data['created']);
        $article = new BlogArticle($data, $view_format);
        return $article;
    }
    
    public function view(): object
    {
        $view = (object)[
            'items' => [],
            'pager' => null
        ];
        $current_page = isset($_GET['page']) ? max((int)$_GET['page'], 0) : 0;
        $total_items = sql_select(from: 'articles')->count();
        if ($total_items > self::ITEMS_PER_PAGE) {
            $view->pager = new Pager($total_items, self::ITEMS_PER_PAGE);
        }
        $offset = $current_page * self::ITEMS_PER_PAGE;
        foreach ($this->loadArticlesData(self::ITEMS_PER_PAGE, true, $offset) as $data) {
            $view->items[] = $this->getArticleFromData($data, BlogArticle::VIEW_MODE_PREVIEW);
        }
        return $view;
    }
}
