<?php

namespace Blog\Modules\Template;

use Twig\Markup;

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
        $output = app()->twig()->render($this->twigTplName(), $this->data());
        if ($this->safety()) {
            $output = new Markup($output, CHARSET);
        }
        return $output;
    }

    public function setNamespace(string $name): void
    {
        $this->namespace = $name;
        return;
    }

    public function twigTplName(): string
    {
        $namespace = isset($this->namespace) ? '@' . $this->namespace . '/' : '';
        return $namespace . $this->template_name . $this->template_extension;
    }

    public function data(): array
    {
        return $this->data;
    }

    /**
     * @param string[]|string $data 
     */
    public function set(string|array $data, $value = null): self
    {
        if (is_array($data) && is_null($value)) {
            foreach ($data as $key => $val) {
                if (!is_string($key)) {
                    continue;
                }
                $this->set($key, $val);
            }
        } else {
            $this->data[$data] = $value;
        }
        return $this;
    }

    /**
     * Get or set current statement of usage of global variables in the template
     */
    public function useGlobals(?bool $usage = null)
    {
        if (is_null($usage)) {
            /** @return bool */
            return $this->use_globals;
        }
        /** @return void */
        $this->use_globals = $usage;
        return;
    }

    protected function setGlobals()
    {
        $this->set('langcode', app()->getLangcode());
        $this->set('charset', CHARSET);
        if (user()->name()) {
            $this->set('user_name', user()->name());
        }
        if (user()->mail()) {
            $this->set('user_mail', user()->mail());
        }
    }

    public function safety(?bool $is_safe = null): bool|self
    {
        if (is_null($is_safe)) {
            return $this->is_safe;
        } else {
            $this->is_safe = $is_safe;
            return $this;
        }
    }
}
