<?php

namespace Blog\Interface\Form;

use Blog\Interface\TemplateInterface;
use Blog\Modules\Template\Element;
use Blog\Modules\TemplateFacade\Title;

/**
 * It using BEM-model class naming with underscore `_` separator for parts `block_block-mod__element_element-mod`
 */
class Form implements FormInterface, TemplateInterface
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
     * @var array<string, ?string> $fields_section Array with defined fields that attached into section
     */
    protected array $fields_section = [];

    /**
     * @var array<string, int> $fields_order Array with defiend fields sorting order weight
     */
    protected array $fields_order = [];

    /**
     * @var array<int|string, array<string, int>> $fields_order_tree is an automatically generated 2D array
     * 
     * Array with sorted fields. It's flushing every time when new field defined or changed field section or weight.
     * 
     * Each array key matchs to the name of defined section `<section_name> => <fields_pool>`
     * 
     * But array index `0` contains fields that aren't attached to any section.
     * 
     * Second level of array contains `<field_name> => <weight>` pairs as array `key => value`.
     */
    protected array $fields_order_tree;

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
    protected string $self_dir;

    /**
     * Form title element
     */
    protected Title $title;

    /**
     * Form title size that correspondes with `<h1>` - `<h6>` HTML tag
     */
    protected int $title_size = 2;

    /**
     * Form title content
     */
    protected string $title_content;

    /**
     * Form template object
     */
    protected Element $form_template;

    /**
     * @var array<string, bool> $prepared_element
     */
    protected array $prepared_elements = [];

    /**
     * Statement to use hidden csrf-token field
     */
    protected bool $use_csrf;

    /**
     * array with attributes for form
     */
    protected array $attributes = [];

    public function __construct(
        ?string $name = null
    ) {
       $this->setName($name);       
       $f = parseClassname(static::class);
       $dir = COREDIR . $f->namespace . '/src/templates/';
       ffpath($dir);
       $this->self_dir = $dir;
    }

    public function __toString()
    {
        return (string)$this->render();
    }
    
    public function template(): Element
    {
        if (!isset($this->form_template)) {
            app()->twig_add_namespace($this->self_dir, 'form-interface');
            $this->form_template = new Element('form');
            $this->form_template->setName('@form-interface/form');
        }
        return $this->form_template;
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
            $classlist = preg_split('/\s+/', $classlist);
        }
        foreach ($classlist as $class) {
            $class = normalizeClassname($class);
            if ($class && !in_array($class, $this->classlist)) {
                array_push($this->classlist, $class);
            }
        }
        return $this;
    }

    public function setClassMod(?string $mod): self
    {
        if (!$mod) {
            unset($this->default_class_mod);
        } else {
            $this->default_class_mod = $mod;
        }
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
    
    public function getDefaultClasslist(): array
    {
        if (!$this->use_default_class) {
            return [];
        }
        $classlist = [$this->default_class];
        if ($mod = $this->default_class_mod ?? $this->name()) {
            array_push($classlist, $this->default_class . bemmod($mod));
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

    public function getItemClasslist(string $bem_element, ?string $bem_element_mod = null, bool $return_string = false): array|string
    {
        $classlist = [];
        $bem_element = bemelem($bem_element);
        $bem_mod = $bem_element_mod ? bemmod($bem_element_mod) : $bem_element_mod;
        if ($this->use_custom_nested_class) {
            $classlist = $this->getClasslist();
        } else if ($this->use_default_class) {
            $classlist = $this->getDefaultClasslist();
        }
        foreach ($classlist as $i => $class) {
            $class = $class . $bem_element;
            $classlist[$i] = $class;
            if ($bem_mod) {
                $classlist[] = $class . $bem_mod;
            }
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

    public function setField(string $name, string $type = 'text', int $order = 0, ?string $section = null): FormField
    {
        $field = new FormField($name, $this, $type);
        $name = $field->name();
        $this->fields[$name] = $field;
        $this->setFieldSection($name, $section);
        $this->setFieldOrder($name, $order);
        return $this->fields[$name];
    }

    public function setSubmit(int $order = 0, ?string $section = null): FormField
    {
        return $this->setField('submit', 'submit', $order, $section);
    }

    public function setFieldSection(string $name, ?string $section = null): void
    {
        $this->fields_section[$name] = $section;
        unset($this->fields_order_tree);
    }

    public function setFieldOrder(string $name, int $order): void
    {
        $this->fields_order[$name] = $order;
    }

    public function getFieldsTree(): array
    {
        if (!isset($this->fields_order_tree)) {
            $order_tree = [0 => []];
            foreach ($this->fields_section as $fname => $section) {
                if (!$section || !$this->s($section)) {
                    $section = 0;
                }
                $order_tree[$section][$fname] = $this->fields_order[$fname] ?? 0;
            }
            foreach ($order_tree as $i => $stack) {
                asort($stack, SORT_NUMERIC);
            }
            $this->fields_order_tree = $order_tree;
        }
        return $this->fields_order_tree;
    }

    public function getSectionFields(string $section_name): array
    {
        $tree = $this->getFieldsTree();
        return $tree[$section_name] ?? [];
    }

    public function f(string $name): ?FormField
    {
        return $this->field($name);
    }

    public function field(string $name): ?FormField
    {
        return $this->fields[$name] ?? null;
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function setSection(string $name, int $order = 0): FormSection
    {
        $section = new FormSection($name, $this);
        $name = $section->name();
        $this->sections[$name] = $section;
        $this->setSectionOrder($name, $order);
        return $this->sections[$name];
    }

    public function setSectionOrder(string $name, int $order = 0): void
    {
        if (!isset($this->sections[$name])) {
            return;
        }
        $this->sections_order[$name] = $order;
        unset($this->sections_stack);
    }

    public function getSectionsOrder(): array
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

    public function section(string $name): ?FormSection
    {
        return $this->sections[$name] ?? null;
    }

    public function s(string $name): ?FormSection
    {
        return $this->section($name);
    }

    public function sections(): array
    {
        return $this->sections;
    }

    public function title(): ?Title
    {
        if (!isset($this->title)) {
            $this->title = new Title($this->title_size);
        }
        return $this->title;
    }

    public function setTitle(?string $content, int $size = 2): void
    {
        if (empty($content)) {
            unset($this->title_content);
        } else {
            $this->title_content = $content;
        }
    }

    public function setTitleSize(int $size): void
    {
        $this->title_size = $size;
    }

    public function getTitleSize(): int
    {
        return $this->title_size;
    }

    public function useCsrf(bool $use = true): void
    {
        $this->use_csrf = $use;
    }
    
    public function setAttribute(string $name, ?string $value = null, bool $data_attribute = false): self
    {
        if ($data_attribute) {
            // remove manualy provided data-prefix
            $name = preg_replace('/^\W*data\W+/', '', $name);
        }
        $name = kebabCase($data_attribute ? "data {$name}" : $name);
        $this->attributes[$name] = $value;
        return $this;
    }
    


    // ==================================================================================
    // -------------------------------- RENDER LOGIC ------------------------------------
    // ----------------------------------------------------------------------------------

    public function render(): Element
    {
        // TODO: complete changes affecting on rendered form element
        $this->prepareForm();
        $this->prepareHiddenFields();
        $this->prepareFormTitle();
        $this->prepareFormBody();
        return $this->template();
    }

    protected function prepareForm(): void
    {
        foreach ($this->attributes as $name => $value) {
            $this->template()->setAttr($name, $value);
        }
        $this->template()->setAttr('action', $this->action());
        $this->template()->setAttr('method', $this->method());
        $this->template()->addClass($this->getClasslist());
        $this->template()->setId($this->id());
    }

    protected function prepareHiddenFields(): void
    {
        if ($this->use_csrf ?? false) {
            $this->setField('csrf', 'hidden')->setValue(
                csrf(false)->get()
            );
        }
        /**
         * @var FormField $field
         */
        foreach ($this->fields() as $name => $field) {
            if (!$field->isHidden()) {
                continue;
            }
            $this->template()->content()->add($field);
            unset(
                $this->fields[$name],
                $this->fields_order[$name],
                $this->fields_section[$name]
            );
        }
    }

    protected function prepareFormTitle(): void
    {
        if (empty($this->title_content ?? null)) {
            return;
        }
        $this->title()->size($this->title_size);
        $this->title()->set($this->title_content);
        $this->title()->addClass(
            $this->getItemClasslist('header')
        );
        $this->template()->content()->add(
            $this->title()
        );
    }

    protected function prepareFormBody(): void
    {
        $fields_tree = $this->getFieldsTree();

        /** @var string[] $sections */
        foreach ($this->getSectionsOrder() as $sections) {
            foreach ($sections as $s) {
                $this->template()->content()->add(
                    $this->s($s)?->render()
                );
            }
        }

        /**
         * @var string $f field name
         * @var int $order field order
         * */
        foreach ($fields_tree[0] as $f => $order) {
            $this->template()->content()->add(
                $this->f($f)?->render()
            );
        }
    }
    
    // ----------------------------------------------------------------------------------
    // -------------------------------- RENDER LOGIC ------------------------------------
    // ==================================================================================
}
