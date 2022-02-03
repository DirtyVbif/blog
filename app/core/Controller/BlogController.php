<?php

namespace Blog\Controller;

class BlogController extends BaseController
{
    public function prepare(): void
    {
        parent::prepare();
        // add main page elements
        app()->page()->setAttr('class', 'page_blog');
        return;
    }

    public function getTitle(): string
    {
        return t('blog');
    }

    public function getArticles(int $limit = 0, bool $order_desc = false): array
    {
        $sql = sql_select(
            ['id', 'title', 'summary', 'body', 'created', 'status', 'alias', 'preview_src', 'preview_alt'],
            'articles'
        );
        $sql->limit($limit);
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
            ['id', 'title', 'summary', 'body', 'created', 'status', 'alias', 'preview_src', 'preview_alt'],
            'articles'
        );
        $sql->where(condition: ['id' => $id]);
        return $sql->first();
    }
}
