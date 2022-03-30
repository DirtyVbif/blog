<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

abstract class TemplateFacade
{
    protected Element $tpl;
    protected bool $renderable = true;
    
    abstract public function tpl(): Element;

    public function __toString()
    {
        return (string)$this->render();
    }

    public function render()
    {
        return $this->renderable ? $this->tpl()->render() : '';
    }

    public function setAttr(string $name, ?string $value = null, bool $data_attribute = false): self
    {
        if ($data_attribute) {
            return $this->setData($name, $value);
        }
        $this->tpl()->setAttr($name, $value);
        return $this;
    }

    public function setData(string $name, ?string $value = null): self
    {
        $this->tpl()->wrapper()->setData($name, $value);
        return $this;
    }

    public function addClass(string|array $classes): self
    {
        $this->tpl()->addClass($classes);
        return $this;
    }

    public function setRenderable(bool $renderable = true): self
    {
        $this->renderable = $renderable;
        return $this;
    }

    public function renderable(): bool
    {
        return $this->renderable;
    }
}
