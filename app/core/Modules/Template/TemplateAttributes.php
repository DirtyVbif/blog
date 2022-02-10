<?php

namespace Blog\Modules\Template;

class TemplateAttributes extends BaseTemplateElement
{
    protected array $attributes = [];
    protected array $classlist = [];

    public function render()
    {
        $stack = [];
        foreach ($this->attributes as $name => $value) {
            $stack[] = is_null($value) ? $name : "{$name}=\"{$value}\"";
        }
        $classlist = $this->renderClasses();
        if (empty($stack) && !$classlist) {
            return '';
        }
        $output = $classlist . ' ' . implode(' ', $stack);
        return $this->markup($output);
    }

    public function set(string $name, ?string $value = null): self
    {
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

    public function addClass(string $class_string): self
    {
        $classes = preg_split('/\s+/', $class_string);
        foreach ($classes as $i => $class) {
            if (!$class) {
                unset($classes[$i]);
            }
            $classes[$i] = preg_replace('/\W+/', '-', strtolower($class));
        }
        $this->classlist = array_merge($this->classlist, $classes);
        return $this;
    }

    public function classList(bool $as_string = false): array|string
    {
        return $as_string ? implode(' ', $this->classlist) : $this->classlist;
    }

    public function renderClasses(): string
    {
        if (empty($this->classlist)) {
            return '';
        }
        return ' class="' . $this->classList(true) . '"';
    }

    public function setId(string $id): self
    {
        $this->attributes['id'] = $id;
        app()->builder()->useId($id);
        return $this;
    }
}
