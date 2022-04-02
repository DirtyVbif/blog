<?php

namespace Blog\Modules\Template;

/**
 * Renderable element contains render logic and can be converted into string by magic @method __toString() using @method render().
 */
abstract class RenderableElement
{
    abstract public function render(): string|\Twig\Markup;

    public function __toString()
    {
        return (string)$this->render();
    }
}
