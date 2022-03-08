<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\TemplateFacade\TemplateFacade;
use Blog\Request\RequestPrototype;

abstract class EntityPrototype extends TemplateFacade
{
    public const ENTITY_PK = 'eid';
    public const ENTITY_TABLE = 'entities';
    public const ENTITY_COLUMNS = ['id' => self::ENTITY_PK, 'created', 'updated', 'etid'];

    protected array $data = [];
    protected bool $exists;
    protected bool $loaded;
    
    abstract public static function create(RequestPrototype $request, ?array $data = null): bool;
    abstract public static function getSqlTableName(): array|string;
    abstract public static function getSqlTableColumns(): array;
    abstract public static function countItems(): int;

    abstract protected function setLoadedData(array $data): void;

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

    public function load(?int $id = null): void
    {
        if (!is_null($id)) {
            $this->id = $id;
        }
        if ($this->id()) {
            $sql = self::sql();
            $sql->where(condition: ['e.eid' => $this->id()]);
            $this->setLoadedData($sql->all());
        }
        return;
    }

    public static function sql(): SQLSelect
    {
        $sql = sql_select(from: ['e' => static::ENTITY_TABLE]);
        $sql->join(table: static::getSqlTableName(), using: static::ENTITY_PK, type: 'INNER')
            ->join(table: ['ec' => 'entities_comments'], using: static::ENTITY_PK)
            ->join(table: ['et' => 'entities_types'], using: 'etid')
            ->join(table: ['c' => 'comments'], using: 'cid')
            ->columns(static::getSqlTableColumns())
            ->columns([
                'e' => static::ENTITY_COLUMNS,
                'et' => ['type_name' => 'name'],
                'ec' => ['cid', 'deleted'],
                'c' => ['comment_status' => 'status']
            ])
            ->useFunction('e.created', 'unix_timestamp', 'created')
            ->useFunction('e.updated', 'unix_timestamp', 'updated');
        return $sql;
    }

    public static function delete(int $id): bool
    {
        $sql = sql_delete(self::ENTITY_TABLE);
        $sql->where(['eid' => $id]);
        return (bool)$sql->delete();
    }
}