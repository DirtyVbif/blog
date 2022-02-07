<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Request\BaseRequest;

abstract class BaseEntity
{
    protected string|array $table_name;
    protected array $table_columns_query;
    protected array $data;
    protected bool $is_exists;

    abstract protected function setEntityDefaults(): void;

    /**
     * Set condition for entity selection query
     */
    abstract protected function preprocessSqlSelect(SQLSelect $sql): void;

    /**
     * Create new entity from data
     */
    abstract public function create(BaseRequest $data): self;

    public function __construct(
        protected int $id
    ) {
        $this->setEntityDefaults();
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
            $sql = sql_select(from: $this->getTableName());
            $sql->columns($this->getColumnsQuery());
            $this->preprocessSqlSelect($sql);
            $this->data = $sql->first();
        } else {
            $this->data = [];
        }
        $this->is_exists = !empty($this->data);
        return;
    }

    /**
     * Get entity storage name
     */
    protected function getTableName(): string
    {
        return $this->table_name;
    }

    protected function getColumnsQuery(): array
    {
        return $this->table_columns_query;
    }

    /**
     * Get entity id
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * Current entity status - is it exists or not.
     */
    public function exists(): bool
    {
        return $this->is_exists;
    }
}
