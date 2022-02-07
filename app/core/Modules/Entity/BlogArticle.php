<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
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

    protected string $view_mode;

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
        }
        $this->setViewMode($view_mode);
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

    protected function setEntityDefaults(): void
    {
        $this->table_name = ['a' => 'articles'];
        $this->table_columns_query = [
            'a' => ['id', 'title', 'summary', 'body', 'alias', 'body', 'created', 'updated', 'preview_src', 'preview_alt', 'author', 'views'],
            'ac' => ['cid']
        ];
    }

    protected function preprocessSqlSelect(SQLSelect &$sql): void
    {
        $sql->join(['ac' => 'article_comments'], on: ['a.id' => 'ac.aid']);
        $sql->where(['a.id' => $this->id()]);
        return;
    }

    public function create(BaseRequest $data): self
    {
        return $this;
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
}
