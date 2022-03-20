<?php

namespace Blog\Modules\Template\Components;

use Blog\Modules\Template\TemplateAttributes;

trait TemplateAttributesMethods
{
    protected TemplateAttributes $attributes;
    
    public function attributes(): TemplateAttributes
    {
        if (!isset($this->attributes)) {
            $this->attributes = new TemplateAttributes;
        }
        return $this->attributes;
    }

    public function setAttr(string $name, ?string $value = null): self
    {
        $this->attributes()->set($name, $value);
        return $this;
    }

    /**
     * Set new data attribute
     * 
     * @param string $name data attribute name without `data-` prefix
     */
    public function setData(string $name, ?string $value = null): self
    {
        $name = strPrefix($name, 'data-');
        $this->setAttr($name, $value);
        return $this;
    }

    public function setAttribute(string $name, ?string $value = null): self
    {
        $this->setAttr($name, $value);
        return $this;
    }

    /**
     * @param string[]|string $classes
     */
    public function addClass(string|array $classes): self
    {
        if (is_array($classes)) {
            foreach ($classes as $class) {
                $this->addClass($class);
            }
        } else {
            $this->attributes()->addClass($classes);
        }
        return $this;
    }

    public function setId(string $id): self
    {
        $this->attributes()->setId($id);
        return $this;
    }
}
