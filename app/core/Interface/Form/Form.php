<?php

namespace Blog\Interface\Form;

use Blog\Modules\Template\Element;
use Blog\Modules\TemplateFacade\Title;

/**
 * It using BEM-model class naming with underscore `_` separator for parts `block_block-mod__element_element-mod`
 */
class Form extends AbstractFormElement implements FormInterface
{
    /**
     * contains default form template class attribute
     */
    protected string $default_class = 'form';

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
     * @var array<string, bool> $prepared_element
     */
    protected array $prepared_elements = [];

    /**
     * Statement to use hidden csrf-token field
     */
    protected bool $use_csrf;

    public function __construct(
        ?string $name = null
    ) {
       $this->setName($name);       
       $f = parseClassname(static::class);
       $dir = COREDIR . $f->namespace . '/src/templates/';
       ffpath($dir);
       $this->self_dir = $dir;
    }

    protected function target(): Element
    {
        return $this->template();
    }

    public function title(): ?Title
    {
        if (!isset($this->title)) {
            $this->title = new Title($this->title_size);
        }
        return $this->title;
    }

    public function setName(?string $form_name): void
    {
        if ($form_name) {
            $this->name = $form_name;
        }
        if ($this->isRendered()) {
            $this->refreshRender();
        }
    }

    public function name(): ?string
    {
        return $this->name ?? null;
    }

    public function addClass(string|array $classlist): self
    {
        parent::addClass($classlist);
        if ($this->isRendered() && !empty($this->new_classes)) {
            $this->template()->addClass($this->new_classes);
        }
        return $this;
    }

    public function useCustomClassForChildren(bool $use): self
    {
        $this->use_custom_nested_class = $use;
        if ($this->isRendered()) {
            $this->refreshRender();
        }
        return $this;
    }
    
    public function getDefaultClasslist(): array
    {
        if (!$this->use_default_class) {
            return [];
        }
        $classlist = [$this->default_class];
        if ($mod = $this->getClassMod() ?? $this->name() ?? null) {
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

    public function getChildClass(string $bem_element, ?string $bem_element_mod = null, bool $return_string = false): array|string
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

    public function setId(string $id): void
    {
        $this->id = kebabCase($id);
        if ($this->isRendered()) {
            $this->refreshRender();
        }
    }

    public function id(): ?string
    {
        if ($id = $this->id ?? null) {
            return $id;
        } else if ($name = $this->name()) {
            return  'form-' . kebabCase($name);
        }
        return 'form';
    }

    public function setMethod(string $method): void
    {
        $this->method = strtoupper($method);
        if ($this->isRendered()) {
            $this->template()->setAttr('method', $method);
        }
    }

    public function method(): string
    {
        return $this->method;
    }

    public function setAction(string $path): void
    {
        $this->action = $path;
        if ($this->isRendered()) {
            $this->template()->setAttr('action', $path);
        }
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
        if ($this->isRendered()) {
            $this->refreshRender();
        }
        return $this->fields[$name];
    }

    public function setSubmit(int $order = 0, ?string $section = null): FormField
    {
        return $this->setField('submit', 'submit', $order, $section);
    }

    public function set(string $name, string $value): FormField
    {
        $name = $this->setField($name, 'hidden')
            ->setValue($value)
            ->name();
        return $this->f($name);
    }

    public function setFieldSection(string $name, ?string $section = null): void
    {
        $this->fields_section[$name] = $section;
        unset($this->fields_order_tree);
        if ($this->isRendered()) {
            $this->refreshRender();
        }
    }

    public function setFieldOrder(string $name, int $order): void
    {
        $this->fields_order[$name] = $order;
        if ($this->isRendered()) {
            $this->refreshRender();
        }
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
        if ($this->isRendered()) {
            $this->refreshRender();
        }
        return $this->sections[$name];
    }

    public function setSectionOrder(string $name, int $order = 0): void
    {
        if (!isset($this->sections[$name])) {
            return;
        }
        $this->sections_order[$name] = $order;
        unset($this->sections_stack);
        if ($this->isRendered()) {
            $this->refreshRender();
        }
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

    public function setTitle(?string $content, int $size = 2): void
    {
        if (empty($content)) {
            unset($this->title_content);
        } else {
            $this->title_content = $content;
        }
        if ($this->isRendered()) {
            $this->title()->set($content ?? '');
            $this->title()->setRenderable(empty($content) ? false : true);
        }
    }

    public function setTitleSize(int $size): void
    {
        $this->title_size = $size;
        if ($this->isRendered()) {
            $this->title()->size($size);
        }
    }

    public function getTitleSize(): int
    {
        return $this->title_size;
    }

    public function useCsrf(bool $use = true): void
    {
        $this->use_csrf = $use;
        if ($this->isRendered()) {
            $this->refreshRender();
        }
    }

    public function contains(string $field_type): bool
    {
        foreach ($this->fields() as $field) {
            if ($field->type() === $field_type) {
                return true;
            }
        }
        return false;
    }



    // ==================================================================================
    // --------------------------------- STATEMENTS -------------------------------------
    // ----------------------------------------------------------------------------------

    protected function isPageTemplateAffected(): bool
    {
        return $this->statement_template_affect ?? false;
    }

    // ----------------------------------------------------------------------------------
    // --------------------------------- STATEMENTS -------------------------------------
    // ==================================================================================
    


    // ==================================================================================
    // -------------------------------- RENDER LOGIC ------------------------------------
    // ----------------------------------------------------------------------------------

    public function refreshRender(): void
    {
        if (!$this->isRendered()) {
            return;
        }
        $this->form_template = new Element('form');
        $this->form_template->setName('@form-interface/form');
        $this->title = new Title($this->title_size);
        foreach ($this->sections() as $s) {
            $s->refreshRender();
        }
        foreach ($this->fields() as $f) {
            $f->refreshRender();
        }
        $this->statement_rended = false;
    }

    public function render(): Element
    {
        if (!$this->isRendered()) {
            $this->prepareForm();
            $this->prepareHiddenFields();
            $this->prepareFormTitle();
            $this->prepareFormBody();
            $this->statement_rended = true;
        }
        $this->affectPageTemplate();
        return $this->template();
    }

    protected function prepareForm(): void
    {
        $this->template()->wrapper()->set('form');
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
            $this->getChildClass('header')
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
                    $this->s($s)
                );
            }
        }

        /**
         * @var string $f field name
         * @var int $order field order
         * */
        foreach ($fields_tree[0] as $f => $order) {
            $this->template()->content()->add(
                $this->f($f)
            );
        }
    }

    protected function affectPageTemplate(): void
    {
        // TODO: add Mediator::class for source files to make it public
        if ($this->isPageTemplateAffected()) {
            return;
        }
        page()->useCss('css/form.min');
        if ($this->contains('password')) {
            page()->useJs('js/pw-switch.min');
        }
        $this->statement_template_affect = true;
    }
    
    // ----------------------------------------------------------------------------------
    // -------------------------------- RENDER LOGIC ------------------------------------
    // ==================================================================================
}
