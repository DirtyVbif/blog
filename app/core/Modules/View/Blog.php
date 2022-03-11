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
        $article = null;
        // check if argument is article id
        if (is_numeric($argument)) {
            // try to load article by article id
            $article = new Article($argument);
            // check if article has an alias
            if ($article->hasAlias()) {
                // redirect to named url (alias) for article
                app()->router()->redirect($article->url());
            }
        } else {
            // try to load article by url alias
            $article = new Article(0);
            $article->loadByAlias($argument);
        }
        if (!$article->exists()) {
            // if no article was loaded by provided argument (id or alias) then request is bad
            return false;
        }
        // set page meta
        app()->page()->setMetaTitle($article->title() . ' | mublog.site');
        app()->page()->setMeta('description', [
            'name' => 'description',
            'content' => $article->get('summary')
        ]);
        app()->page()->setMeta('canonical', [
            'rel' => 'canonical',
            'href' => fullUrlTo($article->url())
        ], 'link');
        app()->page()->setMeta('shortlink', [
            'rel' => 'shortlink',
            'href' => fullUrlTo('blog/' . $article->id())
        ], 'link');
        // set page title
        app()->controller()->getTitle()->set($article->title());
        // view article edit menu
        $article_menu = app()->builder()->getMenu('article_edit', ['id' => $article->id()]);
        app()->page()->addContent($article_menu);
        // view loaded blog article
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
     * @return Article[] $items
     */
    public function preview(int $limit, string $view_format = Article::VIEW_MODE_TEASER): array
    {
        $items = Article::loadList([
            'limit' => $limit,
            'order' => 'DESC',
            'view_mode' => $view_format,
            'load_with_comments' => false
        ]);
        return $items;
    }
    
    public function view(): object
    {
        $view = (object)[
            'items' => [],
            'pager' => null
        ];
        $current_page = isset($_GET['page']) ? max((int)$_GET['page'], 0) : 0;
        $sql = sql_select(['eid'], ['a' => Article::ENTITY_DATA_TABLE]);
        $sql->where(['status' => 1]);
        $sql->useFunction('a.eid', 'COUNT', 'count');
        $result = $sql->first();
        $total_items = $result['count'];
        if ($total_items > self::ITEMS_PER_PAGE) {
            $view->pager = new Pager($total_items, self::ITEMS_PER_PAGE);
        }
        $offset = $current_page * self::ITEMS_PER_PAGE;
        $view->items = Article::loadList([
            'limit' => self::ITEMS_PER_PAGE,
            'offset' => $offset,
            'order' => 'DESC',
            'view_mode' => Article::VIEW_MODE_PREVIEW,
            'load_with_comments' => true
        ]);
        return $view;
    }

    public static function lastUpdate(): int
    {
        $comments_update = sql()->query('SELECT UNIX_TIMESTAMP(MAX(`created`)) as time FROM `comments`;')->fetch();
        $articles_update = sql()->query('SELECT UNIX_TIMESTAMP(MAX(`updated`)) as time FROM `entities` WHERE `etid` = 1;')->fetch();
        return max($comments_update['time'], $articles_update['time']);
    }
}
