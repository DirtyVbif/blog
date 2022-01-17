<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\BaseTemplate;

abstract class TemplateFacade
{
    protected BaseTemplate $tpl;
    protected bool $renderable = true;

    abstract public function tpl();

    public function __toString()
    {
        return (string)$this->render();
    }

    public function render()
    {
        return $this->renderable ? $this->tpl()->render() : '';
    }

    public function setAttr(string $attr_name, string $value): self
    {
        $this->tpl()->setAttr($attr_name, $value);
        return $this;
    }

    public function addClass(string $class_string): self
    {
        $this->tpl()->addClass($class_string);
        return $this;
    }
}
