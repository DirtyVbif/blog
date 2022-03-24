<?php

namespace Blog\Modules\Template;

use Blog\Modules\TemplateFacade\Title;
use Twig\Markup;

class Page extends BaseTemplate
{
    use Components\TemplateAttributesMethods;

    protected Title $title;
    protected array $css_external = [];
    protected array $css_internal = [
        '/css/style.min.css'
    ];
    protected array $js = [];
    protected array $js_order = [];
    protected array $content = [];
    protected array $modal = [];
    protected Element $content_tpl;
    /** @var Element[] $meta */
    protected array $meta = [];
    protected string $meta_title;

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

    public function useJs(string $name, int $order = 2, ?string $load_type = 'async', ?string $content = null): self
    {
        $name = strPrefix(strSuffix($name, '.js'), '/');
        if (!isset($this->js_order[$order])) {
            $this->js_order[$order] = [];
        }
        $this->js[$name] = [
            'src' => is_null($content) ? $name : null,
            'type' => $load_type,
            'content' => is_null($content) ? null : new Markup($content, CHARSET)
        ];
        if (!in_array($name, $this->js_order[$order])) {
            array_push($this->js_order[$order], $name);
        }
        return $this;
    }

    public function useCss(string $name, bool $internal = false): self
    {
        $name = strPrefix(strSuffix($name, '.css'), '/');
        if (!$internal && !in_array($name, $this->css_external)) {
            array_push($this->css_external, $name);
        } else if ($internal && !in_array($name, $this->css_internal)) {
            array_push($this->css_internal, $name);
        }
        return $this;
    }

    public function content(): Element
    {
        if (!isset($this->content_tpl)) {
            $this->content_tpl = new Element('main');
            $this->content_tpl->addClass('container container_main');
        }
        return $this->content_tpl;
    }

    public function render()
    {
        $this->useGlobals(true);
        if (!empty($this->content)) {
            $this->content()->setContent(implode($this->content));
            $this->data['page']['content'] = $this->content();
        }
        $this->data['meta'] = $this->meta;
        $this->data['title'] = $this->meta_title ?? null;
        $this->data['attributes'] = $this->attributes();
        $this->data['css'] = $this->css_external;
        $this->data['css_internal'] = $this->css_internal;
        $this->data['js'] = $this->getJsSrc();
        $this->data['page']['title'] = $this->getTitle();
        $this->data['page']['modal'] = $this->getModal();
        $this->data['page']['messenger'] = msgr();
        $this->data['system_log'] = app()->logger();
        return parent::render();
    }

    protected function getJsSrc(): array
    {
        $array = [];
        if (app()->config('development')->livereload) {
            $content = "document.write('<script src=\"http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1\"></' + 'script>');";
            $this->useJs('_livereload', load_type: null, content: $content);
        }
        foreach ($this->js_order as $stack) {
            foreach ($stack as $name) {
                $array[] = $this->js[$name];
            }
        }
        if (app()->config('development')->js) {
            $suffix = '?t=' . time();
        } else {
            $suffix = '?v=' . stok(':[site|v]');
        }
        foreach ($array as &$js) {
            $js['src'] .= $suffix;
        }
        return $array;
    }

    public function setTitle($content): self
    {
        if (!isset($this->title)) {
            $this->title = new Title;
        }
        $this->title->set($content);
        return $this;
    }

    public function getTitle(): ?Title
    {
        return $this->title ?? null;
    }

    protected function getModal(): ?Element
    {
        if (empty($this->modal)) {
            return null;
        }
        $modal = new Element;
        $modal->addClass('container container_modal');
        $modal->setAttr('role', 'alert');
        $content = implode($this->modal);
        $modal->setContent($content);
        return $modal;
    }

    public function setHeader(BaseTemplate $header): self
    {
        $this->data['page']['header'] = $header;
        return $this;
    }

    public function setFooter(BaseTemplate $footer): self
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

    public function setMetaTitle(string $title): self
    {
        $this->meta_title = $title;
        if (!$this->getMeta('og:title')) {
            $this->setMeta('og:title', [
                'property' => 'og:title',
                'content' => $title
            ]);
        }
        return $this;
    }

    public function setMeta(string $name, array $attributes, string $tag = 'meta'): self
    {
        $this->meta[$name] = new Element($tag);
        foreach ($attributes as $attribute => $value) {
            $this->meta[$name]->setAttr($attribute, $value);
        }
        if (preg_match('/^description$/', $name) && !$this->getMeta('og:description')) {
            $this->setMeta('og:description', [
                'property' => 'og:description',
                'content' => $attributes['content']
            ]);
        }
        return $this;
    }

    public function getMeta(string $name): ?Element
    {
        return $this->meta[$name] ?? null;
    }

    /**
     * Set meta tag `robots` with specified content
     */
    public function metaRobots(string $content): void
    {
        $this->setMeta('robots', [
            'name' => 'robots',
            'content' => $content
        ]);
    }
}
