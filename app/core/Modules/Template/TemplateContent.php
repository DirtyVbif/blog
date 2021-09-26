<?php

namespace Blog\Modules\Template;

use Twig\Markup;

class TemplateContent extends BaseTemplateElement
{
    protected array $content = [];

    public function set($content): self
    {
        $this->content = [$content];
        return $this;
    }

    public function add($content): self
    {
        $this->content[] = $content;
        return $this;
    }

    public function render()
    {
        return implode('', $this->get());
    }

    public function get(): array
    {
        return $this->content;
    }
}
