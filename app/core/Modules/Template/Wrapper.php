<?php

namespace Blog\Modules\Template;

use Twig\Markup;

class Wrapper implements WrapperInterface
{
    public const NO_END_TAG = ['meta', 'link', 'input', 'img'];

    /**
     * Wrapper element html tag name
     */
    protected string $tag;

    /**
     * Wrapper element attributes object
     */
    protected Attributes $attributes;

    /**
     * Render statement for wrapper element
     */
    protected bool $renderable = true;

    public function __construct(string $tag = 'div')
    {
        $this->set($tag);
    }

    public function set(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    public function attributes(): Attributes
    {
        if (!isset($this->attributes)) {
            $this->attributes = new Attributes;
        }
        return $this->attributes;
    }

    public function show(): self
    {
        $this->renderable = true;
        return $this;
    }

    public function hide(): self
    {
        $this->renderable = false;
        return $this;
    }

    public function tag(): ?Markup
    {
        if (!$this->renderable) {
            return null;
        }
        $output = "<{$this->tag}{$this->attributes()}>";
        return new Markup($output, CHARSET);
    }

    public function end(): ?Markup
    {
        if (!$this->renderable || $this->hasEndTag()) {
            return null;
        }
        $output = "</{$this->tag}>";
        return new Markup($output, CHARSET);
    }

    public function hasEndTag(): bool
    {
        return !in_array($this->tag, self::NO_END_TAG);
    }
}
