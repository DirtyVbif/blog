<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\Template\Element;
use Blog\Request\BaseRequest;

class Feedback extends BaseEntity
{
    public const ENTITY_TABLE = 'feedbacks';
    public const ENTITY_COLUMNS = ['id' => 'fbid', 'subject', 'message', 'timestamp', 'headers', 'result'];
    protected const VIEW_MODES = [
        0 => self::VIEW_MODE_FULL
    ];

    public const VIEW_MODE_FULL = 'full';
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
            $this->id = $data['id'] ?? 0;
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
        $this->tpl()->setName('content/feedback--' . $this->view_mode);
        $this->tpl()->setId('feedback-' . $this->id());
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
            $this->data['date'] = new DateFormat($this->data['timestamp'], DateFormat::DETAILED);
            $this->data['headers'] = json_decode($this->data['headers'], true);
        }
        return;
    }

    public static function sql(): SQLSelect
    {
        $sql = sql_select(from: self::ENTITY_TABLE);
        $sql->columns(self::ENTITY_COLUMNS);
        return $sql;
    }

    public function loadById(int $id): self
    {
        $sql = self::sql();
        $sql->where(['fid' => $id]);
        $this->data = $sql->first();
        $this->loaded = true;
        $this->exists = !empty($this->data);
        return $this;
    }

    public function status(): bool
    {
        $status = (int)($this->data['result'] ?? 0);
        return $status;
    }
    
    /**
     * @param \Blog\Request\BaseRequest $data
     */
    public static function create(BaseRequest $data): bool
    {
        // TODO: complete create method
        return false;
    }

    /**
     * @return Feedbacks[] $feedbacks
     */
    public static function loadByIds(array $ids, string $view_mode = self::VIEW_MODE_FULL): array
    {
        $feedbacks = [];
        $sql = self::sql();
        $sql->where(condition: ['fid' => $ids], operator: 'IN');
        foreach ($sql->all() as $feedback) {
            $feedbacks[$feedback['id']] = new self($feedback, $view_mode);
        }
        return $feedbacks;
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

    public static function getSitemapPriority(): float
    {
        return self::SITEMAP_PRIORITY;
    }

    public static function getSitemapChangefreq(): string
    {
        return self::SITEMAP_CHANGEFREQ;
    }
}
