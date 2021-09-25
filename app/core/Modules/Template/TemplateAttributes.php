<?php

namespace Blog\Modules\Template;

class TemplateAttributes extends BaseTemplateElement
{
    protected array $attributes = [];
    public function render()
    {
        $stack = [];
        foreach ($this->attributes as $name => $value) {
            $stack[] = is_null($value) ? $name : "{$name}=\"{$value}\"";
        }
        if (empty($stack)) {
            return '';
        }
        return $this->markup(' ' . implode(' ', $stack));
    }

    public function set(string $name, ?string $value = null): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function get(string $name): ?string
    {
        return $this->attributes[$name] ?? null;
    }
}
