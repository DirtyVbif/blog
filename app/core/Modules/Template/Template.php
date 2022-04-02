<?php

namespace Blog\Modules\Template;

use Twig\Markup;

class Template extends RenderableElement
    implements TemplateInterface,
        TemplateWrapperInterface,
        TemplateContentInterface
{
    public const EXT = '.html.twig';

    /**
     * Template name without extension
     */
    protected string $name;

    /**
     * Template twig namespace
     */
    protected string $namespace = 'default';

    /**
     * Template wrapper element object
     */
    protected Wrapper $wrapper;

    /**
     * @var array<string, mixed> $data template variables data
     */
    protected array $data = [];

    /**
     * @var array<int|string, string|RenderableElement> $content
     */
    protected array $content = [];

    /**
     * Template twig markup escape statement
     */
    protected bool $markup = false;

    /**
     * Custom template render statement
     */
    protected bool $renderable;

    public function __construct(
        string $tag = 'div',
        string $name = 'template'
    ) {
        $this->wrapper()->set($tag);
        $this->setName($name);
    }

    public function wrapper(): Wrapper
    {
        if (!isset($this->wrapper)) {
            $this->wrapper = new Wrapper;
        }
        return $this->wrapper;
    }

    public function render(): string|Markup
    {
        if (!($this->renderable ?? true)) {
            return '';
        }
        $this->set('wrapper', $this->wrapper());
        $this->set('content', implode('', $this->content));
        $output = app()->twig()->render($this->getTemplateName(), $this->data());
        if ($this->markup) {
            $output = new Markup($output, CHARSET);
        }
        return $output;
    }

    protected function getTemplateName(): string
    {
        return  "@{$this->namespace}/{$this->name}" . static::EXT;
    }

    public function setName(string $template_name): self
    {
        $this->name = strSuffix($template_name, static::EXT, true);
        return $this;
    }

    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function use(string $template_name, string $namespace = 'default'): self
    {
        $this->setName($template_name);
        $this->setNamespace($namespace);
        return $this;
    }

    public function set(string|array $data, $value = null): self
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->set($key, $value);
            }
        } else {
            $this->data[$data] = $value;
        }
        return $this;
    }

    public function get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function data(): array
    {
        return $this->data;
    }

    public function setMarkup(bool $statement = true): self
    {
        $this->markup = $statement;
        return $this;
    }

    public function setRenderable(bool $statement): self
    {
        $this->renderable = $statement;
        return $this;
    }

    public function addClass(string|array $classlist): self
    {
        $this->wrapper()->attributes()->addClass($classlist);
        return $this;
    }

    public function setAttribute(string|array $data, ?string $value = null, bool $data_attribute = false): self
    {
        $this->wrapper()->attributes()->set($data, $value, $data_attribute);
        return $this;
    }

    public function setId(string $id): self
    {
        $this->wrapper()->attributes()->setId($id);
        return $this;
    }

    public function setContent(string|RenderableElement $content): self
    {
        $this->content = [$content];
        return $this;
    }

    public function addContent(string|RenderableElement $content, ?string $key = null): self
    {
        if (!is_null($key)) {
            $this->content[$key] = $content;
        } else {
            array_push($this->content, $content);
        }
        return $this;
    }

    public function getContent(string|int $key): string|RenderableElement|null
    {
        return $this->content[$key] ?? null;
    }

    public function unsetContent(string|int $key): TemplateContentInterface
    {
        unset($this->content[$key]);
        return $this;
    }
}
