<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\Template\Element;
use Blog\Modules\User\User;
use Blog\Request\BaseRequest;

class Comment extends BaseEntity
{
    protected const ENTITY_TABLE = 'comments';
    protected const ENTITY_COLUMNS = ['cid', 'pid', 'created', 'name', 'email', 'body', 'status', 'ip'];
    protected const VIEW_MODES = [
        0 => self::VIEW_MODE_FULL,
        1 => self::VIEW_MODE_ARTICLE
    ];

    public const VIEW_MODE_FULL = 'full';
    public const VIEW_MODE_ARTICLE = 'article';
    public const SITEMAP_PRIORITY = 0.1;
    public const SITEMAP_CHANGEFREQ = 'yearly';

    protected SQLSelect $sql;

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
        } else {
            $this->data = $data;
            $this->id = $data['cid'] ?? 0;
        }
        $this->setViewMode($view_mode);
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
        $this->preprocessData();
        $this->tpl()->setName('content/comment--' . $this->view_mode);
        $this->tpl()->setId('comment-' . $this->id());
        foreach ($this->data as $key => $value) {
            $this->tpl()->set($key, $value);
        }
        if (!$this->status()) {
            $this->tpl()->addClass('unpublished');
        }
        if (app()->user()->verifyAccessLevel(4)) {
            $this->tpl()->set('admin_access', true);
        }
        return parent::render();
    }

    protected function preprocessData(): void
    {
        if (!empty($this->data)) {
            $this->data['date'] = new DateFormat($this->data['created']);
        }
        return;
    }

    public function loadById(int $id): self
    {
        $sql = self::sql();
        $sql->where(['cid' => $id]);
        $this->data = $sql->first();
        $this->loaded = true;
        $this->exists = !empty($this->data);
        return $this;
    }

    public function status(): bool
    {
        $status = (int)$this->data['status'] ?? 0;
        return $status;
    }
    
    /**
     * @param \Blog\Request\CommentRequest $data
     */
    public static function create(BaseRequest $data): bool
    {
        if (!$data->isValid()) {
            return false;
        }
        $sql = sql_insert('comments');
        $pid = $data->parent_id ? $data->parent_id : null;
        $sql->set(
            [$pid, time(), $data->name, $data->email, $data->subject, 0, $_SERVER['REMOTE_ADDR']],
            ['pid', 'created', 'name', 'email', 'body', 'status', 'ip']
        );
        $cid = (int)$sql->exe();
        if ($cid) {
            $sql = sql_insert('article_comments');
            $sql->set(
                [$data->article_id, $cid],
                ['aid', 'cid']
            );
            $sql->exe();
            return true;
        }
        return false;
    }

    public static function sql(): SQLSelect
    {
        $sql = sql_select(from: ['c' => self::ENTITY_TABLE]);
        $sql->join(table: ['ac' => 'article_comments'], using: 'cid');
        $sql->join(table: ['a' => 'articles'], using: 'aid');
        $sql->columns([
            'c' => self::ENTITY_COLUMNS,
            'ac' => ['aid', 'deleted'],
            'a' => ['title', 'alias']
        ]);
        return $sql;
    }

    /**
     * @return Comments[] $comments
     */
    public static function loadByIds(array $ids, string $view_mode = self::VIEW_MODE_FULL): array
    {
        $comments = [];
        $sql = self::sql();
        $sql->where(condition: ['c.cid' => $ids], operator: 'IN');
        $sql->andWhere(condition: ['ac.deleted' => 0]);
        if (!app()->user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN)) {
            $sql->andWhere(condition: ['c.status' => 1]);
        }
        foreach ($sql->all() as $comment) {
            $comments[$comment['cid']] = new self($comment, $view_mode);
        }
        return $comments;
    }
    
    /**
     * @return Comments[] $comments
     */
    public static function loadByArticleId(int $aid, string $view_mode = self::VIEW_MODE_FULL): array
    {
        $comments = [];
        $sql = self::sql();
        $sql->where(condition: ['ac.aid' => $aid]);
        $sql->andWhere(condition: ['ac.deleted' => 0]);
        if (!app()->user()->verifyAccessLevel(4)) {
            $sql->andWhere(condition: ['c.status' => 1]);
        }
        foreach ($sql->all() as $comment) {
            $comments[$comment['cid']] = new self($comment, $view_mode);
        }
        return $comments;
    }

    /**
     * @param string $view_mode is name of view mode. Also named constants are available:
     * * Comment::VIEW_MODE_FULL
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

    public function approve(): void
    {
        if ($this->status()) {
            msgr()->warning(t('Comment #@id already published.', ['id' => $this->id()]));
            return;
        }
        $sql = sql_update(['status' => 1], 'comments');
        $sql->where(['cid' => $this->id()]);
        if ($sql->update()) {
            msgr()->notice(t('Comment #@id was published.', ['id' => $this->id()]));
        } else {
            msgr()->error(t('Comment #@id wasn\'t published.', ['id' => $this->id()]));
        }
        return;
    }

    public function delete(): void
    {
        if ($this->deleted) {
            msgr()->warning(t('Comment #@id already deleted.', ['id' => $this->id()]));
            return;
        }
        $sql = sql_update(['deleted' => 1], 'article_comments');
        $sql->where(['cid' => $this->id()]);
        if ($sql->update()) {
            msgr()->notice(t('Comment #@id was deleted.', ['id' => $this->id()]));
        } else {
            msgr()->error(t('Comment #@id wasn\'t deleted.', ['id' => $this->id()]));
        }
        return;
    }

    public static function getSitemapPriority(): float
    {
        return self::SITEMAP_PRIORITY;
    }

    public static function getSitemapChangefreq(): string
    {
        return self::SITEMAP_CHANGEFREQ;
    }
}
