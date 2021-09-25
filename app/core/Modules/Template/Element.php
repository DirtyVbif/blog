<?php

namespace Blog\Modules\Template;

class Element extends BaseTemplate
{
    use Components\TemplateWrapperMethods,
        Components\TemplateContentMethods;
    
    protected string $template_name = 'elements/element';

    public function __construct($tag = 'div') {
        parent::__construct($this->template_name);
        $this->tag($tag);
    }

    public function setName(string $template_name): self
    {
        $this->template_name = $template_name;
        return $this;
    }

    public function render()
    {
        $this->set('wrapper', $this->wrapper());
        $this->set('content', $this->content());
        return parent::render();
    }
}