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
    protected array $modal = [];

    public function __construct(
        protected string $template_name = 'page'
    ) {
        $this->useGlobals(true);
        $this->data['page'] = [];
        if (!cookies()->isCookiesAccepted()) {
            $this->addModal(app()->builder()->getCookieModal());
        }
        parent::__construct($this->template_name);
    }

    public function addModal($content): self
    {
        $this->modal[] = $content;
        return $this;
    }

    public function useJs(string $name, ?string $load_type = 'async', ?string $content = null): self
    {
        $name = strPrefix(strSuffix($name, '.js'), '/');
        $this->js[$name] = [
            'src' => $name,
            'type' => $load_type,
            'content' => $content
        ];
        return $this;
    }

    public function useCss(string $name): self
    {
        $name = strSuffix($name, '.css', true);
        if (!in_array($name, $this->css)) {
            array_push($this->css, $name);
        }
        return $this;
    }

    public function render()
    {
        $this->data['attributes'] = $this->attributes();
        $this->data['css'] = $this->css;
        $this->data['js'] = $this->getJsSrc();
        $this->data['page']['title'] = $this->getTitle();
        $this->data['page']['modal'] = $this->getModal();
        if (!empty($this->content)) {
            $content = new Element('main');
            $content->setAttr('class', 'container container_main');
            $content->setContent(implode($this->content));
            $this->data['page']['content'] = $content;
        }
        return parent::render();
    }

    protected function getJsSrc(): array
    {
        if (app()->config('development')->js) {
            foreach ($this->js as &$js) {
                $js['src'] .= '?t=' . time();
            }
        }
        return $this->js;
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

    public function getModal(): ?Element
    {
        $modal = null;
        if (!empty($this->modal)) {
            $modal = new Element('aside');
            $modal->addClass('container container_modal');
            $modal->setAttr('role', 'alert');
            $content = implode($this->modal);
            $modal->setContent($content);
        }
        return $modal;
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
