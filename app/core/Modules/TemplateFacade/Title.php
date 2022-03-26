<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class Title extends TemplateFacade
{
    protected $content;

    public function __construct(
        protected int $size = 1
    ) {
        $this->size($this->size);
    }
    
    public function tpl(): Element
    {
        if (!isset($this->tpl)) {
            $tag = "h{$this->size}";
            $this->tpl = new Element($tag);
        }
        return $this->tpl;
    }

    public function render()
    {
        if (!(string)($this->content ?? '')) {
            $this->renderable = false;
        } else {
            $this->tpl()->setContent($this->content);
        }
        return parent::render();
    }

    public function set($content): self
    {
        $this->content = $content;
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