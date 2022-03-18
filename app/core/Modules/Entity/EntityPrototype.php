<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\Template\Element;
use Blog\Modules\TemplateFacade\TemplateFacade;
use Blog\Request\RequestPrototype;

abstract class EntityPrototype extends TemplateFacade
{
    public const ENTITY_PK = 'eid';
    public const ENTITY_TABLE = 'entities';
    public const ENTITY_TABLE_ALIAS = 'e';
    public const ENTITY_COLUMNS = ['id' => self::ENTITY_PK, 'created', 'updated', 'etid'];

    protected array $data = [];
    protected bool $exists;
    protected bool $loaded;
    
    abstract public static function create(RequestPrototype $request, ?array $data = null): bool;
    abstract public static function getSqlTableName(): array|string;
    abstract public static function getSqlTableColumns(): array;
    abstract public static function loadList(array $options): array;
    abstract public function url(): ?string;

    public function __construct(
        protected int $id
    ) {
        $this->load();
        if (!$this->exists()) {
            $this->id = 0;
        }
    }

    public function get(string $name)
    {
        return $this->data[$name] ?? null;
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
     * Get entity status if it's exists
     */
    public function exists(): bool
    {
        return $this->exists ?? false;
    }

    protected function loaded(): bool
    {
        return $this->loaded ?? false;
    }

    public static function sql(): SQLSelect
    {
        $sql = sql_select(from: [static::ENTITY_TABLE_ALIAS => static::ENTITY_TABLE]);
        $sql->join(table: static::getSqlTableName(), using: static::ENTITY_PK, type: 'INNER')
            ->join(table: ['et' => 'entities_types'], using: 'etid')
            ->columns(static::getSqlTableColumns())
            ->columns([
                static::ENTITY_TABLE_ALIAS => static::ENTITY_COLUMNS,
                'et' => ['type_name' => 'name']
            ])
            ->useFunction('e.created', 'unix_timestamp', 'created')
            ->useFunction('e.updated', 'unix_timestamp', 'updated');
        return $sql;
    }

    public static function delete(int $id): bool
    {
        $sql = sql_delete(self::ENTITY_TABLE);
        $sql->where([static::ENTITY_PK => $id]);
        return (bool)$sql->delete();
    }

    public static function getNewId(): int
    {
        $sql = sql()->query(
            'SELECT AUTO_INCREMENT'
            . ' FROM information_schema.TABLES'
            . ' WHERE TABLE_SCHEMA = "' . app()->env()->DB['NAME'] . '"'
            . ' AND `TABLE_NAME` = "' . static::ENTITY_TABLE . '";');
        $result = $sql->fetch();
        return $result['AUTO_INCREMENT'];
    }

    public function load(?int $id = null): void
    {
        if (!is_null($id)) {
            $this->id = $id;
        }
        if ($this->id()) {
            $sql = static::sql();
            $sql->where(condition: [static::ENTITY_TABLE_ALIAS . '.' . static::ENTITY_PK => $this->id()]);
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
        $this->data = $data;
        return;
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
}