<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class Title extends TemplateFacade
{
    protected string $content;

    public function __construct(
        protected int $size = 1
    ) {
        $this->size($this->size);
    }
    
    /**
     * @return Element $tpl
     */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            $tag = "h{$this->size}";
            $this->tpl = new Element($tag);
        }
        return $this->tpl;
    }

    public function render()
    {
        if (!isset($this->content) || !$this->content) {
            $this->renderable = false;
        } else {
            $this->tpl()->setContent($this->content);
        }
        return parent::render();
    }

    public function set(string $title_content): self
    {
        $this->content = $title_content;
        return $this;
    }

    public function setAttr(string $name, ?string $value = null): self
    {
        $this->tpl()->setAttr($name, $value);
        return $this;
    }

    public function size(int $size): self
    {
        switch (true) {
            case ($size > 6):
                $this->size = 6;
                break;
            case ($size < 1):
                $this->size = 1;
                break;
            default:
                $this->size = $size;
        }
        return $this;
    }
}