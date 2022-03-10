<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\Template\Element;
use Blog\Modules\User\User;
use Blog\Request\RequestPrototype;
use JetBrains\PhpStorm\ExpectedValues;

class CommentPrototype extends EntityPrototype implements SitemapInterface
{
    public const VIEW_MODE_FULL = 'full';
    public const VIEW_MODE_ARTICLE = 'article';
    public const URL_MASK = '/comments/%d';
    public const ENTITY_URL_MASK = '%s#comment-%d';
    public const ENTITY_PK = 'cid';
    public const ENTITY_TABLE = 'comments';
    public const ENTITY_TABLE_ALIAS = 'c';
    public const ENTITY_COLUMNS = ['cid', 'pid', 'created', 'name', 'email', 'body', 'status', 'ip'];
    public const SITEMAP_PRIORITY = 0.1;
    public const SITEMAP_CHANGEFREQ = 'yearly';

    public static function getSqlTableName(): array|string
    {
        return [self::ENTITY_TABLE_ALIAS => self::ENTITY_TABLE];
    }

    public static function getSqlTableColumns(): array
    {
        return [self::ENTITY_TABLE_ALIAS => self::ENTITY_COLUMNS];
    }

    public static function getSitemapPriority(): float
    {
        return self::SITEMAP_PRIORITY;
    }

    public static function getSitemapChangefreq(): string
    {
        return self::SITEMAP_CHANGEFREQ;
    }

    public static function countItems(): int
    {
        return sql_select(from: self::ENTITY_TABLE)->count();
    }

    public static function sql(): SQLSelect
    {
        $sql = sql_select(from: self::getSqlTableName());
        $sql
            ->join(table: ['ec' => 'entities_comments'], using: self::ENTITY_PK)
            ->join(table: ['e' => 'entities'], using: 'eid')
            ->join(table: ['et' => 'entities_types'], using: 'etid');
        $sql->columns(self::getSqlTableColumns());
        $sql->columns([
            'e' => ['created', 'updated', 'etid'],
            'ec' => ['eid', 'deleted'],
            'et' => ['e_type' => 'name']
        ]);
        $sql->useFunction('c.created', 'UNIX_TIMESTAMP', 'created')
            ->useFunction('e.created', 'UNIX_TIMESTAMP', 'entity_created')
            ->useFunction('e.updated', 'UNIX_TIMESTAMP', 'entity_updated');
        return $sql;
    }

    /**
     * @param array $options recieves SQL QUERY SELECT options:
     * * array key @var int 'limit'
     * * array key @var int 'offset'
     * * array key @var string 'order' => ASC or DESC
     * * array key @var string 'view_mode'
     * 
     * @return CommentPrototype[]
     */
    public static function loadList(array $options): array
    {
        /** @var CommentPrototype[] $comments */
        $comments = [];
        $sql = sql_select(from: self::getSqlTableName());
        $sql->join(table: ['ec' => 'entities_comments'], using: 'cid')
            ->join(table: ['e' => 'entities'], using: 'eid')
            ->join(table: ['et' => 'entities_types'], using: 'etid');
        $sql->columns(self::getSqlTableColumns())
            ->columns([
                'e' => ['eid'],
                'et' => ['entity_type' => 'name']
            ]);
        $sql->where(['ec.deleted' => 0]);
        $sql->limit($options['limit'] ?? null);
        $sql->limitOffset($options['offset'] ?? null);
        $sql->order('c.created', $options['order'] ?? 'ASC');
        $sql->useFunction('c.created', 'UNIX_TIMESTAMP', 'created');
        if (!user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN)) {
            $sql->andWhere(['c.status' => 1]);
        }
        /** @var EntityPrototype[] $comments */
        $entities = [];
        foreach ($sql->all() as $cdata) {
            $comments[$cdata['cid']] = new self($cdata, $options['view_mode'] ?? self::VIEW_MODE_FULL);
            if (!isset($entities[$cdata['eid']])) {
                $entity = EntityFactory::load(0, $cdata['entity_type']);
                $entity->load($cdata['eid'], false);
                $entities[$cdata['eid']] = $entity;
            }
            $comments[$cdata['cid']]->tpl()->set('title', $entities[$cdata['eid']]?->title());
            $comments[$cdata['cid']]->tpl()->set(
                'url',
                sprintf(
                    self::ENTITY_URL_MASK,
                    $entities[$cdata['eid']]?->url(),
                    $comments[$cdata['cid']]->id()
                )
            );
        }
        return $comments;
    }

    public static function approve(int $id): bool
    {
        $sql = sql_update(['status' => 1], self::ENTITY_TABLE);
        $sql->where([self::ENTITY_PK => $id]);
        return (bool)$sql->update();
    }

    public static function delete(int $id): bool
    {
        $sql = sql_update(['deleted' => 1], 'entities_comments');
        $sql->where([self::ENTITY_PK => $id]);
        return (bool)$sql->update();
    }
    
    /**
     * @param \Blog\Request\CommentRequest $request
     */
    public static function create(RequestPrototype $request, ?array $data = null): bool
    {
        if (!$request->isValid()) {
            return false;
        }
        $sql = sql_insert('comments');
        $pid = $request->parent_id ? $request->parent_id : null;
        $status = (int)user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN);
        $sql->set(
            [$pid, user()->id(), time(), $request->name, $request->email, $request->subject, $status, $_SERVER['REMOTE_ADDR']],
            ['pid', 'uid', 'created', 'name', 'email', 'body', 'status', 'ip']
        );
        $sql->useFunction('created', 'FROM_UNIXTIME');
        sql()->startTransation();
        $rollback = true;
        if ($cid = (int)$sql->exe()) {
            $sql = sql_insert('entities_comments');
            $sql->set(
                [$request->entity_id, $cid],
                ['eid', 'cid']
            );
            if ($sql->exe(true)) {
                $rollback = false;
                $request->complete();
            }
        }
        sql()->commit($rollback);
        return !$rollback;
    }

    public function __construct(
        int|array $data = 0,
        #[ExpectedValues(self::VIEW_MODE_FULL, self::VIEW_MODE_ARTICLE)]
        protected string $view_mode = self::VIEW_MODE_FULL
    ) {
        if (is_array($data)) {
            $this->setLoadedData($data);
        } else {
            parent::__construct($data);
        }
    }

    public function status(): bool
    {
        return (int)($this->data['status'] ?? 0);
    }

    /**
     * @return Element $tpl
     */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element;
        }
        return $this->tpl;
    }

    public function render()
    {
        $admin_access = user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN);
        if (!$this->status() && !$admin_access) {
            return '';
        } else if (!$this->status()) {
            $this->tpl()->addClass('unpublished');
        }
        $this->tpl()->setName('content/comment--' . $this->view_mode);
        $this->tpl()->setId('comment-' . $this->id());
        foreach ($this->data as $key => $value) {
            $this->tpl()->set($key, $value);
        }
        if ($admin_access) {
            $this->tpl()->set('admin_access', true);
        }
        return parent::render();
    }

    public function load(?int $id = null, bool $load_comments = true): void
    {
        if (!is_null($id)) {
            $this->id = $id;
        }
        if ($this->id()) {
            $sql = self::sql();
            $sql->where(condition: ['c.cid' => $this->id()]);
            $this->setLoadedData($sql->all());
        }
        return;
    }

    protected function setLoadedData(array $data): void
    {
        if (!arrayIsFlat($data)) {
            // TODO: set throwable error
            pre([
                'error' => 'Provided comment data is not compatable with Entity/Comment::class',
                'data' => $data
            ]);
            die;
        }
        if ($data['cid'] ?? false) {
            $data['id'] = $data['cid'];
            unset($data['cid']);
        }
        $this->data = $data;
        $this->preprocessData();
    }

    protected function preprocessData(): void
    {
        if (!empty($this->data)) {
            $this->data['date'] = new DateFormat($this->data['created']);
        }
        return;
    }

    public function url(): ?string
    {
        if ($this->id()) {
            return sprintf(self::URL_MASK, $this->id());
        }
        return null;
    }
}
