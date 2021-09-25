<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\BaseTemplate;
use Blog\Modules\Template\Element;

class Title extends TemplateFacade
{
    /**
     * @return Element $tpl
     */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element('h1');
        }
        return $this->tpl;
    }

    public function set(string $title_content): self
    {
        $this->tpl()->content()->set($title_content);
        return $this;
    }

    public function setAttr(string $name, ?string $value = null): self
    {
        $this->tpl()->setAttr($name, $value);
        return $this;
    }
}