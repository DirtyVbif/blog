<?php

namespace Blog\Modules\Template;

use Blog\Modules\TemplateFacade\Title;

class Page extends BaseTemplate
{
    use Components\TemplateAttributesMethods;

    protected Title $title;
    protected array $css = [
        'style.min'
    ];
    protected array $js = [];
    protected array $content = [];

    public function __construct(
        protected string $template_name = 'page'
    ) {
        $this->data['page'] = [];
        parent::__construct($this->template_name);
    }

    public function useJs(string $name, bool $async = true): self
    {
        return $this;
    }

    public function useCss(string $name): self
    {
        $name = preg_replace('/\.css$/', '', $name);
        if (!in_array($name, $this->css)) {
            array_push($this->css, $name);
        }
        return $this;
    }

    public function render()
    {
        $this->data['langcode'] = app()->getLangcode();
        $this->data['attributes'] = $this->attributes();
        $this->data['css'] = $this->css;
        if ($title = $this->getTitle()) {
            $this->data['page']['title'] = $title;
        }
        if (!empty($this->content)) {
            $content = new Element('main');
            $content->setAttr('class', 'container container_main');
            $content->setContent(implode($this->content));
            $this->data['page']['content'] = $content;
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

    public function setFooter(PageFooter $footer): self
    {
        $this->data['page']['footer'] = $footer;
        return $this;
    }

    public function addContent($content): self
    {
        if (is_array($content)) {
            foreach ($content as $part) {
                $this->addContent($part);
            }
        } else if (
            is_string($content)
            || (is_object($content) && method_exists($content, '__toString'))
        ) {
            $this->content[] = $content;
        }
        return $this;
    }
}
