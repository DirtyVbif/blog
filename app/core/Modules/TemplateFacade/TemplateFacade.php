<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\BaseTemplate;

abstract class TemplateFacade
{
    protected BaseTemplate $tpl;

    abstract public function tpl();

    public function __toString()
    {
        return (string)$this->render();
    }

    public function render()
    {
        return $this->tpl()->render();
    }
}
