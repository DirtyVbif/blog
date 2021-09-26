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
}
