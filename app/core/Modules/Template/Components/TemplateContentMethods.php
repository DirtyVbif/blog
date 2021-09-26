<?php

namespace Blog\Modules\Template\Components;

use Blog\Modules\Template\TemplateContent;

trait TemplateContentMethods
{
    protected TemplateContent $content;
    
    public function content(): TemplateContent
    {
        if (!isset($this->content)) {
            $this->content = new TemplateContent;
        }
        return $this->content;
    }

    public function setContent($content): self
    {
        $this->content()->set($content);
        return $this;
    }
}
