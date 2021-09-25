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

    public function setAttribute(string $name, ?string $value = null): self
    {
        $this->setAttr($name, $value);
        return $this;
    }
}
