<?php

namespace Blog\Interface\Form;

use Blog\Interface\RenderableInterface;
use Blog\Interface\TemplateInterface;
use Blog\Modules\Template\Element;

abstract class AbstractFormElement implements TemplateInterface, RenderableInterface
{
    protected Element $template;

    /**
     * @var string[] contains custom template classes
     */
    protected array $classlist = [];

    /**
     * Currently added classes in last call @method addClass()
     */
    protected array $new_classes = [];

    /**
     * BEM-model modificator for default classes
     */
    protected string $default_class_mod;

    /**
     * Statement for using default classes
     */
    protected bool $use_default_class = true;

    /**
     * Array with template attributes
     */
    protected array $attributes = [];

    /**
     * Current template render statement
     */
    protected bool $statement_render;

    abstract public function render(): Element;
    abstract public function refreshRender(): void;
    abstract protected function target(): Element;

    public function __toString()
    {
        return (string)$this->render();
    }

    public function template(): Element
    {
        if (!isset($this->template)) {
            $this->template = new Element;
        }
        return $this->template;
    }

    public function setAttribute(string $name, ?string $value = null, bool $data_attribute = false): self
    {
        if ($data_attribute) {
            // remove manualy provided data-prefix
            $name = preg_replace('/^\W*data\W+/', '', $name);
        }
        $name = kebabCase($data_attribute ? "data {$name}" : $name);
        $this->attributes[$name] = $value;
        if ($this->isRendered()) {
            $this->target()->setAttr($name, $value);
        }
        return $this;
    }

    public function addClass(string|array $classlist): self
    {
        if (is_string($classlist)) {
            $classlist = preg_split('/\s+/', $classlist);
        }
        $this->new_classes = [];
        foreach ($classlist as $class) {
            $class = normalizeClassname($class);
            if (!in_array($class, $this->classlist)) {
                array_push($this->classlist, $class);
                $this->new_classes[] = $class;
            }
        }
        return $this;
    }

    public function setDefaultClassMod(?string $mod): self
    {
        if (!$mod) {
            unset($this->default_class_mod);
        } else {
            $this->default_class_mod = $mod;
        }
        if ($this->isRendered()) {
            $this->refreshRender();
        }
        return $this;
    }

    public function useDefaultClass(bool $use): self
    {
        $this->use_default_class = $use;
        if ($this->isRendered()) {
            $this->refreshRender();
        }
        return $this;
    }

    protected function getClassMod(): ?string
    {
        if (empty($this->default_class_mod ?? null)) {
            return null;
        }
        return bemmod($this->default_class_mod);
    }

    protected function isRendered(): bool
    {
        return $this->statement_render ?? false;
    }
}
