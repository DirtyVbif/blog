<?php

namespace Blog\Modules\Template;

interface WrapperInterface
{
    /**
     * Set html tag name for wrapper element
     */
    public function set(string $html_tag): self;

    /**
     * Get access to the wrapper element attributes object
     */
    public function attributes(): Attributes;

    /**
     * Output escaped for twig wrapper open tag with attributes or null if wrapper is hidden
     */
    public function tag(): ?\Twig\Markup;

    /**
     * Output escaped for twig end tag for wrapper element if it is renderable
     */
    public function end(): ?\Twig\Markup;

    /**
     * Set wrapper element statement to be renderable
     */
    public function show(): self;

    /**
     * Set wrapper element statement not to render
     */
    public function hide(): self;

    /**
     * Check if current wrapper element has html close tag
     */
    public function hasEndTag(): bool;
}
