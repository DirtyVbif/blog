<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\Template\Element;
use Blog\Request\RequestPrototype;
use Twig\Markup;

class Article extends BaseEntity implements SitemapInterface
{
    public const VIEW_MODE_FULL = 'full';
    public const VIEW_MODE_TEASER = 'teaser';
    public const VIEW_MODE_PREVIEW = 'preview';
    public const TYPE_ID = 1;                       # Entity type id `article` = 1
    protected const VIEW_MODES = [
        0 => self::VIEW_MODE_FULL,
        1 => self::VIEW_MODE_TEASER,
        2 => self::VIEW_MODE_PREVIEW
    ];
    public const ENTITY_DATA_TABLE = 'entities_article_data';
    public const ENTITY_DATA_COLUMNS = ['title', 'summary', 'body', 'alias', 'status', 'preview_src', 'preview_alt', 'author', 'views'];
    public const SITEMAP_PRIORITY = 0.3;
    public const SITEMAP_CHANGEFREQ = 'monthly';
    public const DEFAULT_PREVIEW_SRC = '/images/article-preview-default.webp';
    public const DEFAULT_PREVIEW_ALT = 'Article preview image';
    public const DEFAULT_AUTHOR = 'mublog.site';

    protected string $view_mode;
    protected SQLSelect $sql;
    protected array $comments;
    protected bool $comments_loaded = false;
    protected bool $comments_preloaded;
    protected int $comments_count;
    protected bool $has_alias;

    public static function getSqlTableName(): array|string
    {
        return ['a' => self::ENTITY_DATA_TABLE];
    }

    public static function getSqlTableColumns(): array
    {
        return ['a' => self::ENTITY_DATA_COLUMNS];
    }

    public static function countItems(): int
    {
        return sql_select(from: self::ENTITY_DATA_TABLE)->count();
    }

    /**
     * @param int|array $data is an id of article that must be loaded or already loaded article data.
     * If integer id provided as `int $data` then article will be automatically loaded from storage.
     * Else if array with article data provided as `array $data` then article wouldn't be loaded from storage and accept provided data.
     */
    public function __construct(
        int|array $data,
        string $view_mode = self::VIEW_MODE_FULL
    ) {
        if (is_int($data)) {
            parent::__construct($data);
        } else if ($data) {
            $this->data = $data;
            $this->comments_preloaded = false;
        } else {
            $this->data = [];
        }
        $this->preprocessData();
        $this->setViewMode($view_mode);
    }

    protected function preprocessData(): void
    {
        if (!empty($this->data)) {
            $this->data['url'] = $this->url();
            $this->data['date'] = new DateFormat($this->data['created']);
            $this->data['comments_count'] = $this->getCommentsCount();
        }
        return;
    }

    /**
     * @return Element $tpl;
     */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element('article');
        }
        return $this->tpl;
    }

    public function render()
    {
        $this->tpl()->setName('content/article--' . $this->view_mode);
        foreach ($this->data as $key => $value) {
            if ($key === 'body') {
                $value = new Markup($value, CHARSET);
            }
            $this->tpl()->set($key, $value);
        }
        return parent::render();
    }

    public function loadById(int $id): self
    {
        $sql = self::sql();
        $sql->where(condition: ['e.eid' => $id]);
        $result = $sql->all();
        $this->setLoadedData($result);
        return $this;
    }

    public function loadByAlias(string $alias): self
    {
        $sql = self::sql();
        $sql->where(condition: ['a.alias' => $alias]);
        $result = $sql->all();
        $this->setLoadedData($result);
        $this->preprocessData();
        return $this;
    }

    protected function setLoadedData(array $data): void
    {
        $this->comments = [];
        if (empty($data)) {
            $this->data = [];
        } else {
            $columns = array_merge(self::ENTITY_COLUMNS, self::ENTITY_DATA_COLUMNS);
            foreach ($columns as $key => $column) {
                $key = is_numeric($key) ? $column : $key;
                $this->data[$key] = $data[0][$key];
            }
            $comments_count = 0;
            foreach ($data as $row) {
                if (!$row['cid']) {
                    continue;
                }
                $this->comments[$row['cid']] = [
                    'deleted' => $row['deleted']
                ];
                if ($row['deleted'] == 0) {
                    $comments_count++;
                }
            }
            $this->comments_count = $comments_count;
            $this->id = $this->data['id'];
        }
        $this->is_exists = !empty($this->data);
        $this->loaded = true;
        $this->comments_preloaded = true;
        return;
    }

    /**
     * @param string $view_mode is name of view mode. Also named constants are available:
     * * BlogArticle::VIEW_MODE_FULL
     * * BlogArticle::VIEW_MODE_TEASER
     * * BlogArticle::VIEW_MODE_PREVIEW
     */
    public function setViewMode(string $view_mode): self
    {
        if (in_array($view_mode, self::VIEW_MODES)) {
            $this->view_mode = $view_mode;
        } else {
            $this->view_mode = self::VIEW_MODE_FULL;
        }
        return $this;
    }

    /**
     * @return Comment[] $comments
     */
    public function getComments(): array
    {
        if (!$this->comments_loaded && $this->comments_preloaded && !empty($this->comments)) {
            $this->comments = Comment::loadByIds(array_keys($this->comments), Comment::VIEW_MODE_ARTICLE);
            $this->comments_loaded = true;
        } else if (!$this->comments_loaded && !$this->comments_preloaded) {
            $this->comments = Comment::loadByArticleId($this->id(), Comment::VIEW_MODE_ARTICLE);
            $this->comments_loaded = true;
        }
        return $this->comments;
    }

    public function getCommentsCount(): int
    {
        if (!isset($this->comments_count)) {
            $this->comments_count = 0;
            /** @var Comment $comment */
            foreach ($this->getComments() as $comment) {
                if ($comment->status()) {
                    $this->comments_count++;
                }
            }
        }
        return $this->comments_count;
    }

    /**
     * @param \Blog\Request\ArticleRequest $request
     */
    public static function create(RequestPrototype $request, ?array $data = null): bool
    {
        $time = time();
        $sql = sql_insert(self::ENTITY_TABLE);
        $sql->set(
            [$time, $time, self::TYPE_ID],
            ['created', 'updated', 'etid']
        );
        $sql->useFunction('created', 'FROM_UNIXTIME')
            ->useFunction('updated', 'FROM_UNIXTIME');
        sql()->startTransation();
        $rollback = true;
        if ($eid = $sql->exe()) {
            $sql = sql_insert(self::ENTITY_DATA_TABLE);
            $columns = self::ENTITY_DATA_COLUMNS;
            $columns[] = 'eid';
            $values = [
                $request->title, $request->summary,
                $request->body, $request->alias, $request->status,
                $request->preview_src, $request->preview_alt,
                $request->author, 0, $eid
            ];
            $sql->set($values, $columns);
            if ($sql->exe(true)) {
                $rollback = false;
                $request->complete();
            }
        }
        sql()->commit($rollback);
        return !$rollback;
    }

    public static function isAliasExists(string $alias): bool
    {
        $sql = sql_select(['eid'], self::ENTITY_DATA_TABLE);
        $sql->where(['alias' => $alias]);
        $result = $sql->exe();
        return !empty($result);
    }

    public static function getSitemapPriority(): float
    {
        return self::SITEMAP_PRIORITY;
    }

    public static function getSitemapChangefreq(): string
    {
        return self::SITEMAP_CHANGEFREQ;
    }

    public static function getUrl(int $id): string
    {
        $self = new self($id);
        return $self->url();
    }

    public function url(): string
    {
        if (!$this->exists()) {
            return '';
        } else if ($this->alias) {
            return "/blog/{$this->alias}";
        }
        return "/blog/{$this->id()}";
    }

    public function hasAlias(): bool
    {
        return (bool)$this->alias;
    }

    public function title(): string
    {
        if (!$this->exists()) {
            return '';
        }
        return $this->title;
    }

    public static function delete(int $id): bool
    {
        $sql = sql_delete(self::ENTITY_TABLE);
        $sql->where(['eid' => $id]);
        return (bool)$sql->delete();
    }
}
