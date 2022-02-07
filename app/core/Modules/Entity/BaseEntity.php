<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\TemplateFacade\TemplateFacade;
use Blog\Request\BaseRequest;

abstract class BaseEntity extends TemplateFacade
{
    protected string|array $table_name;
    protected array $table_columns_query;
    protected array $data;
    protected bool $is_exists;
    protected bool $loaded = false;

    abstract protected function setEntityDefaults(): void;

    /**
     * Set condition for entity selection query
     */
    abstract protected function preprocessSqlSelect(SQLSelect &$sql): void;

    /**
     * Create new entity from data
     */
    abstract public function create(BaseRequest $data): self;

    public function __construct(
        protected int $id
    ) {
        $this->loadEntityData();
        $this->setId();
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
        $this->setEntityDefaults();
        if ($this->id > 0) {
            $sql = sql_select(from: $this->getTableName());
            $sql->columns($this->getColumnsQuery());
            $this->preprocessSqlSelect($sql);
            $this->data = $sql->first();
        } else {
            $this->data = [];
        }
        $this->loaded = true;
        $this->is_exists = !empty($this->data);
        return;
    }

    protected function setId(): void
    {
        $this->id = $this->data['id'] ?? 0;
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
}
