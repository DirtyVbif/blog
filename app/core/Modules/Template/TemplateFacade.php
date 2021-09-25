<?php

namespace Blog\Modules\Template;

abstract class TemplateFacade
{
    protected BaseTemplate $tpl;

    abstract public function tpl(): BaseTemplate;

    public function __toString()
    {
        return (string)$this->render();
    }

    public function render()
    {
        return $this->tpl();
    }
}
