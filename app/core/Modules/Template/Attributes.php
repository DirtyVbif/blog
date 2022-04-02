<?php

namespace Blog\Modules\Template;

use Twig\Markup;

class Attributes extends RenderableElement implements AttributesInterface
{
    protected array $data = [];
    protected array $classlist = [];

    public function render(): string|Markup
    {
        if (empty($this->data) && empty($this->classlist)) {
            return '';
        }
        $attributes = [];
        if (!empty($this->classlist)) {
            $attributes[] = 'class="' . implode(' ', $this->classlist) . '"';
        }
        foreach ($this->data as $attribute => $value) {
            $attributes[] = empty($value) ? $attribute : "{$attribute}=\"{$value}\"";
        }
        $output = ' ' . implode(' ', $attributes);
        return new Markup($output, CHARSET);
    }

    public function get(string $name): ?string
    {
        if ($name === 'class') {
            return $this->classlist(true);
        }
        return $this->data[$name] ?? null;
    }

    public function unset(string $name): self
    {
        if ($name === 'class') {
            $this->classlist = [];
        } else {
            unset($this->data[$name]);
        }
        return $this;
    }

    public function set(string|array $data, ?string $value = null, bool $data_attribute = false): self
    {
        if (is_array($data)) {
            foreach ($data as $key => $v) {
                $this->set($key, $v, $data_attribute);
            }
        } else {
            $data = normalizeClassname($data);
        }
        if ($data === 'class' && !$data_attribute) {
            $this->addClass($value);
        } else if ($data === 'id' && !$data_attribute) {
            $this->setId($value);
        } else {
            $k = $data_attribute ? strPrefix($data, 'data-') : $data;
            $this->data[$k] = $value;
        }
        return $this;
    }

    public function setId(string $id): self
    {
        $id = normalizeClassname($id);
        if ($id) {
            $this->data['id'] = $id;
            app()->builder()->useId($id);
        }
        return $this;
    }

    public function addClass(string|array $classlist): self
    {
        if (empty($classlist)) {
            return $this;
        } else if (is_string($classlist)) {
            $classlist = preg_split('/[\s\,]+/', $classlist);
        }
        foreach ($classlist as $class) {
            if (is_array($class)) {
                $this->addClass($class);
                continue;
            } else if (!settype($class, 'string')) {
                continue;
            }
            $class = normalizeClassname($class);
            if (!empty($class) && !in_array($class, $this->classlist)) {
                array_push($this->classlist, $class);
            }
        }
        return $this;
    }

    public function classlist(bool $return_as_string = false): array|string
    {
        return $return_as_string ? implode(' ', $this->classlist) : $this->classlist;
    }
}
