<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\TemplateFacade\TemplateFacade;
use Blog\Request\RequestPrototype;

abstract class BaseEntity extends TemplateFacade
{
    public const ENTITY_TABLE = 'entities';
    public const ENTITY_COLUMNS = ['id' => 'eid', 'created', 'updated', 'etid'];

    protected array $data;
    protected bool $is_exists;
    protected bool $loaded = false;

    /**
     * Create new entity from data
     */
    abstract public static function create(RequestPrototype $request, ?array $data = null): bool;
    abstract public static function getSitemapPriority(): float;
    abstract public static function getSitemapChangefreq(): string;
    abstract public static function getSqlTableName(): array|string;
    abstract public static function getSqlTableColumns(): array;
    abstract public static function countItems(): int;

    abstract public function loadById(int $id): self;
    abstract public function url(): string;
    abstract public function title(): string;

    public function __construct(
        protected int $id
    ) {
        $this->loadEntityData();
        if (!$this->exists()) {
            $this->id = 0;
        }
    }

    public function __get(string $name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }

    protected function loadEntityData(): void
    {
        if ($this->id > 0) {
            $this->loadById($this->id);
        } else {
            $this->data = [];
        }
        $this->loaded = true;
        $this->is_exists = !empty($this->data);
        return;
    }

    /**
     * Get entity id
     */
    public function id(): int
    {
        if (!isset($this->id)) {
            $this->id = $this->data['id'] ?? 0;
        }
        return $this->id;
    }

    /**
     * Current entity status - is it exists or not.
     */
    public function exists(): bool
    {
        if ($this->loaded) {
            return $this->is_exists ?? false;
        }
        return $this->id();
    }

    protected static function getNewId(): int
    {
        $sql = sql()->query(
            'SELECT AUTO_INCREMENT'
            . ' FROM information_schema.TABLES'
            . ' WHERE TABLE_SCHEMA = "' . app()->env()->DB['NAME'] . '"'
            . ' AND `TABLE_NAME` = "entities";');
        $result = $sql->fetch();
        return $result['AUTO_INCREMENT'];
    }

    public static function sql(): SQLSelect
    {
        $sql = sql_select(from: ['e' => 'entities']);
        $sql
            ->join(table: static::getSqlTableName(), using: 'eid', type: 'INNER')
            ->join(table: ['ec' => 'entities_comments'], using: 'eid')
            ->join(table: ['et' => 'entities_types'], using: 'etid');
        $sql->columns(static::getSqlTableColumns());
        $sql->columns([
            'e' => static::ENTITY_COLUMNS,
            'et' => ['type_name' => 'name'],
            'ec' => ['cid', 'deleted']
        ]);
        $sql
            ->useFunction('e.created', 'unix_timestamp', 'created')
            ->useFunction('e.updated', 'unix_timestamp', 'updated');
        return $sql;
    }

    public static function generateUrl(string $type, int $id): string
    {
        switch ($type) {
            case 'article':
                /** @var Article $entity_class */
                return Article::getUrl($id);
                break;
            default:
                return '';
        }
    }

    public static function getTypeById(int $id): string
    {
        $sql = sql_select(from: ['e' => self::ENTITY_TABLE]);
        $sql->join(table: ['et' => 'entities_types'], using: 'etid');
        $sql->columns(['et' => ['name']]);
        $sql->where(['eid' => $id]);
        $result = $sql->first();
        return $result['name'];
    }

    public static function load(int $id): self
    {
        switch (self::getTypeById($id)) {
            case 'article':
                return new Article($id);
            case 'feedback':
                return new Feedback($id);
            default:
                return new self($id);
        }
    }
}
