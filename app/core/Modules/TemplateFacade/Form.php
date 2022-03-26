<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class Form extends TemplateFacade
{
    public function __construct(
        protected string $name,
        string $wrapper_tag = 'div'
    ) {
        $this->tpl()->tag($wrapper_tag);
    }
    
    public function tpl(): Element
    {
        if (!isset($this->tpl)) {
            $tpl = new Element;
            $tpl->setName('forms/' . $this->formName());
            $this->tpl = $tpl;
        }
        return $this->tpl;
    }

    public function formName(): string
    {
        return $this->name;
    }
}
