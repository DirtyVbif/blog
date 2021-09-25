<?php

namespace Blog\Modules\Template;

use Blog\Modules\TemplateFacade\Title;

class Page extends BaseTemplate
{
    use Components\TemplateAttributesMethods;

    protected Title $title;

    public function __construct(
        protected string $template_name = 'page'
    ) {
        $this->data['page'] = [];
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
        $this->data['attributes'] = $this->attributes();
        if ($title = $this->getTitle()) {
            $this->data['page']['title'] = $title;
        }
        return parent::render();
    }

    public function setTitle(string $title_content): self
    {
        if (!isset($this->title)) {
            $this->title = new Title;
        }
        $this->title->set($title_content);
        return $this;
    }

    public function getTitle(): ?Title
    {
        return $this->title ?? null;
    }

    public function setHeader(PageHeader $header): self
    {
        $this->data['page']['header'] = $header;
        return $this;
    }
}
