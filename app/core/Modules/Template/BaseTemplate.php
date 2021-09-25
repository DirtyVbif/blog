<?php

namespace Blog\Modules\Template;

class BaseTemplate
{
    protected array $data = [];
    protected string $namespace;
    protected string $template_extension = '.html.twig';

    public function __construct(
        protected string $template_name
    ) {
        $this->template_name = preg_replace('/(\.html\.twig$)|(\.twig$)/', '', $this->template_name);
        return $this;
    }
    
    public function __toString()
    {
        return (string)$this->render();
    }

    public function render()
    {
        return app()->twig()->render($this->twigTplName(), $this->data());
    }

    public function twigTplName(): string
    {
        $namespace = isset($this->namespace) ? $this->namespace . '@' : '';
        return $namespace . $this->template_name . $this->template_extension;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function set(string $data_key, $value): void
    {
        $this->data[$data_key] = $value;
        return;
    }
}
