<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\Template\Element;
use Blog\Request\BaseRequest;
use Twig\Markup;

class BlogArticle extends BaseEntity
{
    public const VIEW_MODE_FULL = 'full';
    public const VIEW_MODE_TEASER = 'teaser';
    public const VIEW_MODE_PREVIEW = 'preview';
    protected const VIEW_MODES = [
        0 => self::VIEW_MODE_FULL,
        1 => self::VIEW_MODE_TEASER,
        2 => self::VIEW_MODE_PREVIEW
    ];
    protected const ENTITY_TABLE = 'articles';
    protected const ENTITY_COLUMNS = ['id', 'title', 'summary', 'body', 'alias', 'body', 'created', 'updated', 'preview_src', 'preview_alt', 'author', 'views'];

    protected string $view_mode;
    protected SQLSelect $sql;
    protected array $comments;
    protected bool $comments_loaded = false;
    protected bool $comments_preloaded;
    protected int $comments_count;

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
            $this->comments_preloaded = false;
        }
        $this->setViewMode($view_mode);
    }

    protected function preprocessData(): void
    {
        if (!empty($this->data)) {            
            $this->data['url'] = '/blog/' . ($this->data['alias'] ?? $this->data['id']);
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
        $this->preprocessData();
        foreach ($this->data as $key => $value) {
            if ($key === 'body') {
                $value = new Markup($value, CHARSET);
            }
            $this->tpl()->set($key, $value);
        }
        return parent::render();
    }

    protected function sql(): SQLSelect
    {
        if (!isset($this->sql)) {
            $this->sql = sql_select(from: ['a' => self::ENTITY_TABLE]);
            $this->sql->columns([
                'a' => self::ENTITY_COLUMNS,
                'ac' => ['cid']
            ]);
            $this->sql->join(['ac' => 'article_comments'], on: ['a.id', 'ac.aid']);
        }
        return $this->sql;
    }

    public function loadById(int $id): self
    {
        $this->sql()->where(condition: ['a.id' => $id]);
        $this->sql()->andWhere(condition: ['ac.deleted' => 0]);
        $result = $this->sql()->all();
        $this->setLoadedData($result);
        return $this;
    }

    public function loadByAlias(string $alias): self
    {
        $this->sql()->where(condition: ['a.alias' => $alias]);
        $this->sql()->andWhere(condition: ['ac.deleted' => 0]);
        $result = $this->sql()->all();
        $this->setLoadedData($result);
        return $this;
    }

    protected function setLoadedData(array $data): void
    {
        $this->comments = [];
        if (empty($data)) {
            $this->data = [];
        } else {
            foreach (self::ENTITY_COLUMNS as $column) {
                $this->data[$column] = $data[0][$column];
            }
            foreach ($data as $row) {
                if (!$row['cid']) {
                    continue;
                }
                $this->comments[$row['cid']] = [];
            }
            $this->data['comments_count'] = count(array_keys($this->comments));
            $this->id = $this->data['id'];
        }
        $this->is_exists = !empty($this->data);
        $this->loaded = true;
        $this->comments_preloaded = true;
        return;
    }

    public function create(BaseRequest $data): bool
    {
        // TODO: complete creation of article
        pre($data);
        die;
        return false;
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

    public function loadComments(): void
    {
        $sql = sql_select(from: ['ac' => 'article_comments']);
        $sql->join(['c' => 'comments'], using: 'cid');
        $sql->columns([
            'ac' => ['cid'],
            'c' => ['pid', 'created', 'name', 'email', 'body', 'status', 'ip']
        ]);
        $sql->where(['ac.aid' => $this->id()]);
        $comments = $sql->all();
        pre($comments);
        return;
    }

    /**
     * @return Comment[] $comments
     */
    public function getComments(): array
    {
        if (!$this->comments_loaded && $this->comments_preloaded && !empty($this->comments)) {
            $this->comments = Comment::loadByIds(array_keys($this->comments));
            $this->comments_loaded = true;
        } else if (!$this->comments_loaded && !$this->comments_preloaded) {
            $this->comments = Comment::loadByArticleId($this->id());
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
}
