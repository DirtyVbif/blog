<?php

namespace Blog\Modules\Template;

class BaseTemplate
{
    protected array $data = [];
    protected string $namespace;
    protected string $template_extension = '.html.twig';
    protected bool $is_safe = false;
    protected bool $use_globals = false;
    protected array $globals;

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
        if ($this->useGlobals()) {
            $this->setGlobals();
        }
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

    /**
     * Get or set current statement of usage of global variables in the template
     */
    public function useGlobals(?bool $usage = null)
    {
        if (is_null($usage)) {
            /** @return bool */
            return $this->use_globals;
        } else {
            /** @return void */
            $this->use_globals = $usage;
            return;
        }
    }

    protected function setGlobals()
    {
        $this->set('langcode', app()->getLangcode());
    }
}
