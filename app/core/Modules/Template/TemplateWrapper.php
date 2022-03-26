<?php

namespace Blog\Modules\Template;

class TemplateWrapper extends BaseTemplateElement
{
    use Components\TemplateAttributesMethods;

    protected const NOENDTAG = [
        'input',
        'img',
        'link',
        'meta'
    ];
    protected string $name = 'div';
    protected bool $hidden = false;

    /**
     * Set new HTML tag name
     */
    public function set(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get current HTML tag name
     */
    public function get(): string
    {
        return $this->name;
    }

    public function open(): string|\Twig\Markup
    {
        return $this->hidden ? '' : $this->markup("<{$this->name}{$this->attributes()}>");
    }

    public function close(): string|\Twig\Markup
    {
        return ($this->hidden || $this->nullable()) ? '' : $this->markup("</{$this->name}>");
    }

    public function render()
    {
        return $this->open();
    }

    public function hide(): self
    {
        $this->hidden = true;
        return $this;
    }

    public function show(): self
    {
        $this->hidden = false;
        return $this;
    }

    /**
     * Checks if current HTML-element needs closing tag and content
     * 
     * @return true|false
     * * `true` - if element has no need to be closed and can't have any content (such as <img> <input> <link> etc.);
     */
    public function nullable(): bool
    {
        return in_array($this->name, self::NOENDTAG);
    }
}
