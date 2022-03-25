<?php

namespace Blog\Interface\Form;

use Blog\Modules\Template\Element;
use Blog\Modules\TemplateFacade\TemplateFacade;

/**
 * It using BEM-model class naming with underscore `_` separator for parts `block_block-mod__element_element-mod`
 */
class Form extends TemplateFacade implements FormInterface
{
    /**
     * form mask for autogeneratable template id attribute
     */
    protected string $form_id_mask = 'form-%s';

    /**
     * contains default form template class attribute
     */
    protected string $default_class = 'form';

    /**
     * BEM modificator for default form template class attribute
     */
    protected string $default_class_mod;

    /**
     * Indicates form to use default template class attribute
     */
    protected bool $use_default_class = true;

    /**
     * @var string[] contains form custom template classes attribute
     */
    protected array $classlist = [];

    /**
     * Use or not use custom classes from `@var string[] $classlist` for generating classes for nested items
     */
    protected bool $use_custom_nested_class = false;

    /**
     * Form name that will be used for automatic form id and class attributes generating
     */
    protected string $name;

    /**
     * Form template method attribute value
     */
    protected string $method = 'GET';

    /**
     * Form template action attribute value
     */
    protected string $action = '/';

    /**
     * @var FormField[] $fields array with defined form fields
     */
    protected array $fields = [];

    /**
     * Array with defined fields that attached into section
     */
    protected array $fields_section = [];

    /**
     * Array with sorted fields. It's flushing every time when new field defined or changed field section
     */
    protected array $fields_stack;

    /**
     * @var FormSection[] $sections array with defined form sections
     */
    protected array $sections = [];

    /**
     * Array with section sorting order
     */
    protected array $sections_order = [];

    /**
     * Array with sorted sections. It's flushing every time when new section defined or changed section order
     */
    protected array $sections_stack;

    /**
     * Form interface directory absolute path
     */
    protected string $idir;

    public function __construct(
        ?string $name = null
    ) {
       $this->setName($name);       
       $f = parseClassname(static::class);
       $dir = COREDIR . $f->namespace . '/src/templates/';
       ffpath($dir);
       $this->idir = $dir;
    }

    public function setName(?string $form_name): void
    {
        if ($form_name) {
            $this->name = $form_name;
        }
    }

    public function name(): ?string
    {
        return $this->name ?? null;
    }

    public function addClass(string|array $classlist): self
    {
        if (is_string($classlist)) {
            $classlist = preg_split('/[^\w\-]+/', $classlist);
        }
        foreach ($classlist as $class) {
            $class = preg_replace(['/^\s+/', '/\s+$/'], ['', ''], $class);
            if (!in_array($class, $this->classlist)) {
                array_push($this->classlist);
            }
        }
        return $this;
    }

    public function setClassMod(?string $mod): self
    {
        $this->default_class_mod = $mod ? bemmod($mod) : $mod;
        return $this;
    }

    public function useDefaultClass(bool $use): self
    {
        $this->use_default_class = $use;
        return $this;
    }

    public function useClasslistForNestedItems(bool $use): self
    {
        $this->use_custom_nested_class = $use;
        return $this;
    }

    /**
     * Get form default classlist with BEM-model modificator if it specified
     */
    protected function getDefaultClasslist(): array
    {
        if (!$this->use_default_class) {
            return [];
        }
        $classlist = [$this->default_class];
        if ($mod = $this->default_class_mod ?? $this->name()) {
            array_push($classlist, $this->default_class . $mod);
        }
        return $classlist;
    }

    public function getClasslist(bool $return_string = false): array|string
    {
        $classlist = $this->getDefaultClasslist();
        foreach ($this->classlist as $class) {
            array_push($classlist, $class);
        }
        return $return_string ? implode(' ', $classlist) : $classlist;
    }

    public function getItemClasslist(string $item_name, bool $return_string = false): array|string
    {
        $classlist = [];
        $bem_element = $item_name ? bemelem($item_name) : $item_name;
        if ($this->use_custom_nested_class) {
            $classlist = $this->getClasslist();
        } else if ($this->use_default_class) {
            $classlist = $this->getDefaultClasslist();
        }
        foreach ($classlist as $i => $class) {
            $classlist[$i] .= $bem_element;
        }
        return $return_string ? implode(' ', $classlist) : $classlist;
    }

    public function setFormIdMask(string $mask_string): void
    {
        $this->form_id_mask = $mask_string;
    }

    public function setId(string $form_id): void
    {
        $this->id = $form_id;    
    }

    public function id(): ?string
    {
        if ($id = $this->id ?? null) {
            return $id;
        } else if ($name = $this->name()) {
            return sprintf($this->form_id_mask, kebabCase($name));
        }
        return null;
    }

    public function setMethod(string $method): void
    {
        $this->method = strtoupper($method);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function setAction(string $path): void
    {
        $this->action = $path;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function setField(string $name, ?string $section = null): FormFieldInterface
    {
        $this->fields[$name] = new FormField($name);
        $this->setFieldSection($name, $section);
        return $this->fields[$name];
    }

    public function setFieldSection(string $name, ?string $section = null): void
    {
        $this->fields_section[$name] = $section;
        unset($this->fields_stack);
    }

    public function getFieldOrder(): array
    {
        if (!isset($this->fields_stack)) {
            $stack = [0 => []];
            foreach ($this->fields_section as $fname => $section) {
                if (!$section) {
                    $section = 0;
                }
                array_push($stack[$section], $fname);
            }
            $this->fields_stack = $stack;
        }
        return $this->fields_stack;
    }

    public function field(string $name): ?FormFieldInterface
    {
        return $this->field[$name] ?? null;
    }

    public function setSection(string $name, int $order = 0): FormSectionInterface
    {
        $this->sections[$name] = new FormSection($name);
        $this->setSectionOrder($name, $order);
        return $this->sections[$name];
    }

    public function setSectionOrder(string $name, int $order = 0): void
    {
        $this->sections_order[$name] = $order;
        unset($this->sections_stack);
    }

    public function getSectionOrder(): array
    {
        if (!isset($this->sections_stack)) {
            $stack = [];
            foreach ($this->sections_order as $section => $o) {
                if (!isset($stack[$o])) {
                    $stack[$o] = [];
                }
                array_push($stack[$o], $section);
            }
            ksort($stack, SORT_NUMERIC);
            $this->sections_stack = $stack;
        }
        return $this->sections_stack;
    }

    public function section(string $name): ?FormSectionInterface
    {
        return $this->sections[$name] ?? null;
    }

    /**
     * @return Element
     */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element('form');
            app()->twig_add_namespace($this->idir, 'form-interface');
            $this->tpl->setName('@form-interface/form');
        }
        return $this->tpl;
    }
}
