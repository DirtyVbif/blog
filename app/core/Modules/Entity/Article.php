<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Mediators\AjaxResponse;
use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\Template\Element;
use Blog\Request\ArticleRequest;
use Blog\Request\RequestPrototype;
use JetBrains\PhpStorm\ExpectedValues;
use Twig\Markup;

class Article extends EntityPrototype implements SitemapInterface
{
    public const VIEW_MODE_FULL = 'full';
    public const VIEW_MODE_TEASER = 'teaser';
    public const VIEW_MODE_PREVIEW = 'preview';
    public const URL_MASK = '/blog/%s';
    public const ENTITY_COMMENTS_FIELDS = [
        'c_pid' => 'pid', 'c_created' => 'created',
        'c_name' => 'name', 'c_email' => 'email',
        'c_body' => 'body', 'c_status' => 'status',
        'c_ip' => 'ip'
    ];
    public const SESSION_VOTE_KEY = 'entity-id-%d-vote-result';
    
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
    public const ENTITY_DATA_COLUMNS = ['title', 'summary', 'body', 'alias', 'status', 'preview_src', 'preview_alt', 'author', 'views', 'rating'];
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
    /** @var Comment[] $comments */
    protected array $comments;
    protected array $comments_data;
    protected bool $loaded_with_comments;
    /** @var int $comments_count count of comments with published status for loaded article */
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

    public static function shortLink(int $id): string
    {
        return sprintf(self::URL_MASK, $id);
    }

    protected static function changeRating(int $id, int $increment): bool
    {
        if (!$increment) {
            return false;
        }
        $sql = sql_update(['rating' => "{{`rating` + " . $increment . "}}"], self::ENTITY_DATA_TABLE);
        $sql->where([self::ENTITY_PK => $id]);
        return $sql->update();
    }

    public static function updateRating(int $id, array $data, ?AjaxResponse $response = null): bool
    {
        $result = false;
        $session_key = sprintf(self::SESSION_VOTE_KEY, $id);
        $increment = ($data['increment'] ?? false) ? (int)$data['increment'] : null;
        $new_vote_result = ($data['vote_result'] ?? false) ? (int)$data['vote_result'] : null;
        $vote_result = (int)session()->get($session_key);
        if (is_null($increment) && !is_null($new_vote_result)) {
            $result = true;
        } else if (!is_null($increment) || $vote_result) {
            $increment ??= 0;
            if (
                ($increment > 0 && $vote_result <= 0)
                || ($increment < 0 && $vote_result >= 0)
            ) {
                $result = self::changeRating($id, $increment);
                $new_vote_result = $vote_result + $increment;
            }
        }
        if ($result) {
            session()->set($session_key, $new_vote_result);
        } else if ($response) {
            $response->setResponse("New rating for article #{$id} is not acceptable.");
            $response->setCode(406);
        }
        return $result;
    }

    public static function updateViews(int $id, array $data, ?AjaxResponse $response = null): bool
    {
        $result = false;
        // TODO: complete article views updated
        return $result;
    }

    /**
     * Check provided article alias for uniqueness
     */
    public static function isAliasExists(string $alias, ?int $id = null): bool
    {
        $sql = sql_select(['eid'], self::ENTITY_DATA_TABLE);
        $sql->where(['alias' => $alias]);
        if (!is_null($id)) {
            $sql->where(['eid' => $id], not: true);
        }
        $result = $sql->exe();
        return !empty($result);
    }

    /**
     * @param \Blog\Request\ArticleRequest $request
     */
    public static function create(RequestPrototype $request, ?array $data = null): bool
    {
        $time = time();
        $rollback = true;
        $sql = sql_insert(self::ENTITY_TABLE);
        $sql->set(
            [$time, $time, self::ENTITY_TYPE_ID],
            ['created', 'updated', 'etid']
        );
        $sql->useFunction('created', 'FROM_UNIXTIME')
            ->useFunction('updated', 'FROM_UNIXTIME');
        sql()->startTransation();
        if ($eid = $sql->exe()) {
            $sql = sql_insert(self::ENTITY_DATA_TABLE);
            // get entity data columns
            $columns = self::ENTITY_DATA_COLUMNS;
            // unset columns that has automatically generated values
            unset($columns['views'], $columns['rating']);
            // set array with new values
            $values = [];
            foreach ($columns as $name) {
                $values[] = $request->{$name};
            }
            // add new entity id into SQL INSERT STATEMENT
            $columns[] = 'eid';
            $values[] = $eid;
            // make SQL INSERT QUERY
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
     * @param ArticleRequest $request
     */
    public static function edit(int $id, RequestPrototype $request): bool
    {
        $sql = sql_update(table: self::ENTITY_DATA_TABLE);
        $sql->set([
            'title' => $request->title,
            'alias' => $request->alias,
            'preview_src' => $request->preview_src,
            'preview_alt' => $request->preview_alt,
            'summary' => $request->summary,
            'body' => $request->body,
            'author' => $request->author,
            'status' => $request->status
        ]);
        $sql->where([self::ENTITY_PK => $id]);
        $result = (bool)$sql->update();
        if (!$result) {
            pre([
                'error' => 'there was no changes by following sql request:',
                'SQL-QUERY' => htmlspecialchars($sql->raw('bind')),
                'SQL STATEMENT' => $sql
            ]);
        }
        return $result;
    }

    /**
     * @param array $options recieves SQL QUERY SELECT options:
     * * array key @var int 'limit'
     * * array key @var int 'offset'
     * * array key @var string 'order' => ASC or DESC
     * * array key @var string 'view_mode'
     * * array key @var bool 'load_with_comments' => load article with or without comments data
     * @return Article[]
     */
    public static function loadList(array $options = []): array
    {
        $sql = sql_select(columns: ['eid'], from: self::ENTITY_TABLE);
        $sql->limit($options['limit'] ?? null);
        $sql->limitOffset($options['offset'] ?? null);
        $sql->order('created', ($options['order'] ?? 'ASC'));
        $sql->where(['etid' => self::ENTITY_TYPE_ID]);
        $view_mode = $options['view_mode'] ?? self::VIEW_MODE_FULL;
        $articles = [];
        foreach ($sql->all() as $row) {
            if (isset($options['load_with_comments'])) {
                $article = new self(0, $view_mode);
                $article->load($row['eid'], $options['load_with_comments']);
            } else {
                $article = new self($row['eid'], $view_mode);
            }
            $articles[$row['eid']] = $article;
        }
        return $articles;
    }

    public static function sqlJoinComments(): SQLSelect
    {
        $sql = static::sql();        
        $sql->join(table: ['ec' => 'entities_comments'], using: 'eid')
            ->join(table: ['c' => 'comments'], using: 'cid');
        $sql->columns([
            'ec' => ['cid', 'deleted'],
            'c' => self::ENTITY_COMMENTS_FIELDS
        ])->useFunction('c.created', 'UNIX_TIMESTAMP', 'c_created');
        $sql->order('c.created', 'DESC');
        return $sql;
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
     * @param string $view_mode is name of view mode. Also named constants are available
     */
    public function setViewMode(
        #[ExpectedValues(
            self::VIEW_MODE_FULL,
            self::VIEW_MODE_PREVIEW,
            self::VIEW_MODE_TEASER
        )] string $view_mode
    ): self {
        $this->view_mode = $view_mode;
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
        /** @var \BlogLibrary\EntityStats\EntityStats $lib_stats */
        $lib_stats = app()->library('entity-stats');
        $lib_stats->prepareTemplate($this->tpl(), $this->id(), $this->get('type_name'));
        if ($this->view_mode === self::VIEW_MODE_FULL) {
            $lib_stats->use();
        }
        foreach ($this->data as $key => $value) {
            if ($key === 'body') {
                $value = new Markup($value, CHARSET);
            }
            $this->tpl()->set($key, $value);
        }
        return parent::render();
    }

    public function load(?int $id = null, bool $load_comments = true): void
    {
        $this->loaded_with_comments = $load_comments;
        if (!is_null($id)) {
            $this->id = $id;
        }
        if ($this->id()) {
            $sql = $load_comments ? self::sqlJoinComments() : self::sql();
            $sql->where(condition: ['e.eid' => $this->id()]);
            $this->setLoadedData($sql->all());
        }
        return;
    }

    /**
     * Load blog article entity by article alias
     */
    public function loadByAlias(string $alias): self
    {
        $sql = self::sqlJoinComments();
        $sql->where(condition: ['a.alias' => $alias]);
        $this->setLoadedData($sql->all());
        return $this;
    }

    protected function setLoadedData(array $data): void
    {
        if (!empty($data)) {
            $this->data = $data[0];
            $this->id = $this->data['id'];
            $this->comments_data = $this->setLoadedCommentsData($data);
        }
        $this->exists = !empty($this->data);
        $this->loaded = true;
        $this->preprocessData();
        return;
    }

    protected function setLoadedCommentsData(array $data): array
    {
        $comments = [];
        if (!$this->loaded_with_comments) {
            return $comments;
        }
        foreach ($data as $row) {
            if ($row['cid'] && $row['deleted'] == 0) {
                foreach (self::ENTITY_COMMENTS_FIELDS as $alias => $column) {
                    $comments[$row['cid']]['cid'] = $row['cid'];
                    $comments[$row['cid']][$column] = $row[$alias];
                }
            }
        }
        unset($this->data['cid']);
        foreach (self::ENTITY_COMMENTS_FIELDS as $alias => $column) {
            unset($this->data[$alias]);
        }
        return $comments;
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
     * @return Comment[]
     */
    public function getComments(): array
    {
        if (!isset($this->comments)) {
            $this->comments = [];
            if (!$this->loaded_with_comments) {
                // TODO: load comments data for entity
            }
            foreach ($this->comments_data as $i => $comment_data) {
                $this->comments[$i] = new Comment(
                    $comment_data,
                    Comment::VIEW_MODE_ARTICLE
                );
            }
        }
        return $this->comments;
    }

    /**
     * @return int count of comments with published status for loaded article
     */
    public function getCommentsCount(): int
    {
        if (!isset($this->comments_count) && $this->loaded_with_comments) {
            $this->comments_count = 0;
            foreach ($this->comments_data as $comment_data) {
                $this->comments_count += $comment_data['status'] ?? 0;
            }
        }
        return $this->comments_count ?? 0;
    }

    public function title(): ?string
    {
        return $this->get('title');
    }

    public function rating(): int
    {
        return $this->get('rating');
    }
}
