<?php

namespace Blog\Modules\Template;

class Page extends BaseTemplate
{
    public function __construct(protected string $template_name = 'page')
    {
        parent::__construct($this->template_name);
    }

    public function useJs(string $name): self
    {
        return $this;
    }

    public function useCss(string $name): self
    {
        return $this;
    }

    public function render()
    {
        $this->data['langcode'] = app()->getLangcode();
        return parent::render();
    }
}
