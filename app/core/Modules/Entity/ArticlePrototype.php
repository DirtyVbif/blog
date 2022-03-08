<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\Template\Element;
use Blog\Request\RequestPrototype;
use JetBrains\PhpStorm\ExpectedValues;
use Twig\Markup;

class Article extends EntityPrototype implements SitemapInterface
{
    public const VIEW_MODE_FULL = 'full';
    public const VIEW_MODE_TEASER = 'teaser';
    public const VIEW_MODE_PREVIEW = 'preview';

    public const URL_MASK = '/blog/%s';
    
    /**
     * Article entity type id is 1
     * 
     * It's equals `article` value from `entities_types` where `etid` = 1
     */
    public const ENTITY_TYPE_ID = 1;

    /**
     * Article entity data table name that contains article data
     */
    public const ENTITY_DATA_TABLE = 'entities_article_data';
    public const ENTITY_DATA_COLUMNS = ['title', 'summary', 'body', 'alias', 'status', 'preview_src', 'preview_alt', 'author', 'views'];
    public const SITEMAP_PRIORITY = 0.3;
    public const SITEMAP_CHANGEFREQ = 'monthly';
    
    /**
     * Default value for article preview image source
     */
    public const DEFAULT_PREVIEW_SRC = '/images/article-preview-default.webp';

    /**
     * Default value for article preview image alt text
     */
    public const DEFAULT_PREVIEW_ALT = 'Article preview image';

    /**
     * Default value for article author
     */
    public const DEFAULT_AUTHOR = 'mublog.site';

    protected string $view_mode;
    protected SQLSelect $sql;
    protected array $comments;
    protected bool $comments_loaded = false;
    protected bool $comments_ids_loaded = false;
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

    public static function getSitemapPriority(): float
    {
        return self::SITEMAP_PRIORITY;
    }

    public static function getSitemapChangefreq(): string
    {
        return self::SITEMAP_CHANGEFREQ;
    }

    public static function getUrl(int $id): ?string
    {
        $self = new self($id);
        return $self->url();
    }

    /**
     * Check provided article alias for uniqueness
     */
    public static function isAliasExists(string $alias): bool
    {
        $sql = sql_select(['eid'], self::ENTITY_DATA_TABLE);
        $sql->where(['alias' => $alias]);
        $result = $sql->exe();
        return !empty($result);
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

    /**
     * @param int|array $data integer entity id of article entity or array with article data.
     * > If parameter $data provided as integer entity id then article will be automatically loaded by entity id from database.
     * > If parameter $data provided as array with article data then article wouldn't be loaded from database and accepts provided data.
     * 
     * @param string $view_mode @see self::class->setViewMode()
     */
    public function __construct(
        int $id = 0,
        string $view_mode = self::VIEW_MODE_FULL
    ) {
        parent::__construct($id);
        $this->setViewMode($view_mode);
    }

    /**
     * Load blog article entity by article alias
     */
    public function loadByAlias(string $alias): self
    {
        $sql = self::sql();
        $sql->where(condition: ['a.alias' => $alias]);
        $this->setLoadedData($sql->all());
        return $this;
    }

    /**
     * @param string $view_mode is name of view mode. Also named constants are available
     */
    public function setViewMode(
        #[ExpectedValues(
            self::VIEW_MODE_FULL,
            self::VIEW_MODE_PREVIEW,
            self::VIEW_MODE_TEASER
        )] string $view_mode
    ): self {
        if (in_array($view_mode, self::VIEW_MODES)) {
            $this->view_mode = $view_mode;
        } else {
            $this->view_mode = self::VIEW_MODE_FULL;
        }
        return $this;
    }

    public function url(): ?string
    {
        if ($this->get('alias')) {
            return sprintf(self::URL_MASK, $this->get('alias'));
        } else if ($this->id()) {
            return sprintf(self::URL_MASK, $this->id());
        }
        return null;
    }

    public function hasAlias(): bool
    {
        return (bool)$this->get('alias');
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

    protected function setLoadedData(array $data): void
    {
        if (!empty($data)) {
            $this->data = $data[0];
            $this->id = $this->data['id'];
            unset(
                $this->data['cid'],
                $this->data['deleted'],
                $this->data['comment_status']
            );
            $this->comments_count = 0;
            foreach ($data as $row) {
                if ($row['cid'] && $row['deleted'] == 0) {
                    $this->comments[$row['cid']] = [
                        'cid' => $row['cid'],
                        'status' => $row['comment_status']
                    ];
                    $this->comments_count += $row['comment_status'];
                }
            }
        }
        $this->comments_ids_loaded = true;
        $this->exists = !empty($this->data);
        $this->loaded = true;
        $this->preprocessData();
        return;
    }

    protected function preprocessData(): void
    {
        if ($this->exists()) {
            $this->data['url'] = $this->url();
            $this->data['date'] = new DateFormat($this->data['created']);
            $this->data['comments_count'] = $this->getCommentsCount();
        }
        return;
    }

    /**
     * @return Comment[] $comments
     */
    public function getComments(): array
    {
        if (!$this->comments_loaded) {
            $this->comments = Comment::loadByIds(array_keys($this->comments), Comment::VIEW_MODE_ARTICLE);
            $this->comments_loaded = true;
        }
        return $this->comments;
    }

    public function getCommentsCount(): int
    {
        return $this->comments_count;
    }

    public function title(): ?string
    {
        return $this->get('title');
    }
}
