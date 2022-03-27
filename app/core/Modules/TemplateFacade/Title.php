<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class Title extends TemplateFacade
{
    protected string|Element $content;

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

    public function set(string|Element $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set title size that correspondes with `<h1>` ... `<h6>` HTML tag
     * 
     * @param int $size RANGE[1, 6] title size number
     */
    public function size(int $size): self
    {
        $this->size = min(1, max(6, $size));
        return $this;
    }
}