<?php

namespace Blog\Modules\Template;

class TemplateAttributes extends BaseTemplateElement
{
    protected array $attributes = [];
    protected array $classlist = [];

    public function render()
    {
        $attributes = $this->attributes;
        if (!empty($this->classlist)) {
            $attributes['class'] = $this->classlist(true);
        }
        foreach ($attributes as $name => $value) {
            $attributes[$name] = empty($value) ? $name : "{$name}=\"{$value}\"";
        }
        return $this->markup(' ' . implode(' ', $attributes));
    }

    public function set(string $name, ?string $value = null): self
    {
        $name = normalizeClassname($name);
        if ($name === 'class' && $value) {
            return $this->addClass($value);
        } else if ($name === 'id' && $value) {
            return $this->setId($value);
        }
        $this->attributes[$name] = $value;
        return $this;
    }

    public function get(string $name): ?string
    {
        return $this->attributes[$name] ?? null;
    }

    public function unset(string $name): self
    {
        unset($this->attributes[$name]);
        return $this;
    }

    /**
     * @param string[]|string $classlist
     */
    public function addClass(string|array $classlist): self
    {
        if (empty($classlist)) {
            return $this;
        } else if (is_string($classlist)) {
            $classlist = preg_split('/\s+/', $classlist);
        }
        foreach ($classlist as $class) {
            if (is_array($class)) {
                $this->addClass($class);
                continue;
            }
            $class = normalizeClassname($class);
            if (empty($class)) {
                continue;
            } else if (!in_array($class, $this->classlist)) {
                $this->classlist[] = $class;
            }
        }
        return $this;
    }

    public function classlist(bool $return_string = false): array|string
    {
        return $return_string ? implode(' ', $this->classlist) : $this->classlist;
    }

    public function setId(string $id): self
    {
        $this->attributes['id'] = $id;
        app()->builder()->useId($id);
        return $this;
    }
}
