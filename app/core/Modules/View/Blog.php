<?php

namespace Blog\Modules\View;

use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\Entity\BlogArticle;
use Blog\Modules\TemplateFacade\Form;
use Blog\Modules\TemplateFacade\Pager;
use Blog\Modules\View\BaseView;

class Blog extends BaseView
{
    protected const ITEMS_PER_PAGE = 12;

    public static function viewBlogArticle(string|int $argument): bool
    {
        // check argument for matching with blog article
        if (is_numeric($argument) && $article = self::getArticleById($argument)) {
            // if argument is numeric and equals to blog article id then redirect to named url alias of that article
            app()->router()->redirect($article->url);
            return true;
        } else if (is_numeric($argument)) {
            // if argument is numeric and there is no blog article with such id then url is unexisting
            return false;
        }
        // try to load article by url alias
        $article = self::getArticleByAlias($argument);
        if (!$article) {
            return false;
        }
        // view loaded blog article
        app()->page()->setTitle($article->title);
        app()->page()->addContent($article);
        $comment_form = new Form('comment');
        $comment_form->tpl()->set('article_id', $article->id);
        $comment_form->tpl()->set('parent_id', 0);
        app()->page()->addContent($comment_form);
        app()->page()->content()->addClass('container_article');
        // TODO: set page meta shortlink
        return true;
    }

    public static function viewBlogPage(): void
    {
        app()->page()->addContent(
            app()->builder()->getBlogPage()
        );
        app()->page()->content()->addClass('container_blog');
        return;
    }

    public static function loadArticlesData(int $limit = 0, bool $order_desc = false, int $offset = 0): array
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

    public static function getArticleById(int $id): ?BlogArticle
    {
        $article = new BlogArticle($id);
        return $article->exists() ? $article : null;
    }

    public static function getArticleByAlias(string $alias): ?BlogArticle
    {
        $data = self::loadArticleDataByColumn('alias', $alias);
        if (empty($data)) {
            return null;
        }
        $article = new BlogArticle($data);
        return $article;
    }

    protected static function loadArticleDataByColumn(string $column, string $search_value): array
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
            $items[] = new BlogArticle($data, $view_format);
        }
        return $items;
    }

    protected static function getArticleFromData(array $data, string $view_format = BlogArticle::VIEW_MODE_FULL): BlogArticle
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
            $view->items[] = self::getArticleFromData($data, BlogArticle::VIEW_MODE_PREVIEW);
        }
        return $view;
    }
}
