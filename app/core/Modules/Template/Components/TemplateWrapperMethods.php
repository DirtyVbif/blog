<?php

namespace Blog\Modules\Template\Components;

use Blog\Modules\Template\TemplateWrapper;

trait TemplateWrapperMethods
{
    protected TemplateWrapper $wrapper;
    
    public function wrapper(): TemplateWrapper
    {
        if (!isset($this->wrapper)) {
            $this->wrapper = new TemplateWrapper;
        }
        return $this->wrapper;
    }

    public function tag(?string $tag_name = null): string|self
    {
        if (is_null($tag_name)) {
            return $this->wrapper()->get();
        }
        $this->wrapper()->set($tag_name);
        return $this;
    }

    public function setAttr(string $name, ?string $value = null): self
    {
        $this->wrapper()->setAttr($name, $value);
        return $this;
    }
}
