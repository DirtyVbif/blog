<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\TemplateFacade\TemplateFacade;
use Blog\Request\BaseRequest;

abstract class BaseEntity extends TemplateFacade
{
    protected array $data;
    protected bool $is_exists;
    protected bool $loaded = false;

    /**
     * Create new entity from data
     */
    abstract public function create(BaseRequest $data): bool;

    abstract public function loadById(int $id): self;

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
}
