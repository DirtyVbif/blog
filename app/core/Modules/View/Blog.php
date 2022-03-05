<?php

namespace Blog\Modules\View;

use Blog\Modules\Entity\Article;
use Blog\Modules\Template\Element;
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
        // set page meta
        app()->page()->setMetaTitle($article->title . ' | mublog.site');
        app()->page()->setMeta('description', [
            'name' => 'description',
            'content' => $article->summary
        ]);
        app()->page()->setMeta('canonical', [
            'rel' => 'canonical',
            'href' => fullUrlTo($article->url)
        ], 'link');
        app()->page()->setMeta('shortlink', [
            'rel' => 'shortlink',
            'href' => fullUrlTo('blog/' . $article->id())
        ], 'link');
        // view loaded blog article
        app()->controller()->getTitle()->set($article->title);
        app()->page()->addContent($article);
        $comment_form = new Form('comment', 'section');
        $comment_form->tpl()->set('entity_id', $article->id());
        $comment_form->tpl()->set('parent_id', 0);
        $comment_form->tpl()->useGlobals(true);
        app()->page()->addContent($comment_form);
        app()->page()->content()->addClass('container_article');
        // pre($article->getComments());
        $comments = new Element('section');
        $comments->setName('blocks/article--comments');
        $comments->set('items', $article->getComments());
        app()->page()->addContent($comments);
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

    /**
     * Loads articles data from storage as array
     * 
     * @return array of articles data with keys specified in \Blog\Modules\Entity\Article::ENTITY_COLUMNS
     */
    public static function loadArticlesData(int $limit = 0, bool $order_desc = false, int $offset = 0): array
    {
        $sql = Article::sql();
        $sql->limit($limit);
        if ($offset) {
            $sql->limitOffset($offset);
        }
        $order = 'ASC';
        if ($order_desc) {
            $order = 'DESC';
        }
        $sql->order('created', $order);
        $items = [];
        foreach ($sql->all() as $row) {
            $items[$row['id']] = $row;
            unset($items[$row['id']]['cid']);
            if ($row['cid']) {
                $items[$row['id']]['comments'][$row['cid']] = [
                    'cid' => $row['cid'],
                    'deleted' => $row['deleted']
                ];
            }
        }
        return $items;
    }

    public static function getArticleById(int $id): ?Article
    {
        $article = new Article($id);
        return $article->exists() ? $article : null;
    }

    public static function getArticleByAlias(string $alias): ?Article
    {
        $article = new Article(0);
        $article->loadByAlias($alias);
        return $article->exists() ? $article : null;
    }

    protected static function loadArticleDataByColumn(string $column, string $search_value): array
    {
        $sql = Article::sql();
        $sql->where(condition: [$column => $search_value]);
        return $sql->first();
    }
    
    /**
     * @return Article[] $items
     */
    public function preview(int $limit, string $view_format = Article::VIEW_MODE_TEASER): array
    {
        $items = [];
        foreach ($this->loadArticlesData($limit, true) as $data) {
            $items[] = new Article($data, $view_format);
        }
        return $items;
    }
    
    public function view(): object
    {
        $view = (object)[
            'items' => [],
            'pager' => null
        ];
        $current_page = isset($_GET['page']) ? max((int)$_GET['page'], 0) : 0;
        $total_items = Article::countItems();
        if ($total_items > self::ITEMS_PER_PAGE) {
            $view->pager = new Pager($total_items, self::ITEMS_PER_PAGE);
        }
        $offset = $current_page * self::ITEMS_PER_PAGE;
        foreach ($this->loadArticlesData(self::ITEMS_PER_PAGE, true, $offset) as $data) {
            $view->items[] = new Article($data, Article::VIEW_MODE_PREVIEW);
        }
        return $view;
    }

    public static function lastUpdate(): int
    {
        $comments_update = sql()->query('SELECT UNIX_TIMESTAMP(MAX(`created`)) as time FROM `comments`;')->fetch();
        $articles_update = sql()->query('SELECT UNIX_TIMESTAMP(MAX(`updated`)) as time FROM `entities` WHERE `etid` = 1;')->fetch();
        return max($comments_update['time'], $articles_update['time']);
    }
}
