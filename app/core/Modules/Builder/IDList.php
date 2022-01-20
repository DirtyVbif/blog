<?php

namespace Blog\Modules\Builder;

class IDList
{
    use \Blog\Components\Singletone;

    protected array $list = [];

    public function use(string $id): bool
    {
        if (!in_array($id, $this->list)) {
            $this->set($id);
            return true;
        }
        return false;
    }

    public function set(string $id): self
    {
        if (!$this->isset($id)) {
            array_push($this->list, $id);
        }
        return $this;
    }

    public function isset(string $id): bool
    {
        return in_array($id, $this->list);
    }
}
