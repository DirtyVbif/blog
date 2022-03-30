<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class Title extends TemplateFacade
{
    protected int $size;
    protected string|Element $content;

    public function __construct(int $size = 1) {
        $this->size($size);
    }
    
    public function tpl(): Element
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element($this->tag());
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
        $this->size = max(1, min(6, $size));
        $this->tpl()->wrapper()->set($this->tag());
        return $this;
    }

    protected function tag(): string
    {
        return "h{$this->size}";
    }
}