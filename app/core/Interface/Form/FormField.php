<?php

namespace Blog\Interface\Form;

use Blog\Modules\Template\Element;
use JetBrains\PhpStorm\ExpectedValues;

class FormField extends AbstractFormElement implements FormFieldInterface
{
    public const ORDER_AFTER_LABEL = 0;
    public const ORDER_BEFORE_LABEL = 1;
    public const ORDER_AFTER_IN_LABEL = 2;
    public const ORDER_BEFORE_IN_LABEL = 3;

    /**
     *  Default BEM-model modificator for input element that generates from input type
     */
    public const DEFAULT_BEM_MODS = [];

    /**
     * Default BEM-model modificator for other elements that generates from input type
     */
    public const DEFAULT_BEM_ELEMENTS = [
        'reset', 'submit', 'checkbox', 'textarea', 'password'
    ];

    /**
     * Field name in underscore_case
     */
    protected string $name;

    /**
     * Field automatically generated id attribute value based on field name and form id
     * 
     * It has patter of `form-name--field-name`
     */
    protected string $id;

    /**
     * Field input value
     */
    protected string $value;

    /**
     * Field input element
     */
    protected Element $input;

    /**
     * Field input line element for input, prefix, suffix and label in some cases
     */
    protected Element $input_line;

    /**
     * Statement for field input required
     */
    protected bool $required;

    /**
     * Field label element
     */
    protected Element $label;

    /**
     * Field label content
     */
    protected Element|string $label_content;

    /**
     * Statement for field label to render it in one line with input field or not
     */
    protected bool $inline_label = false;

    /**
     * Field input placeholder content
     */
    protected string $placeholder;

    /**
     * Order for field and label
     */
    protected int $order = self::ORDER_AFTER_LABEL;

    /**
     * Statement to use or not field and label wrapper parent element
     */
    protected bool $use_wrapper = true;

    /**
     * Field input prefix content
     */
    protected string $prefix;

    /**
     * Field input suffix content
     */
    protected string $suffix;

    /**
     * Field description before input element
     */
    protected string|Element $desc_before_content;

    /**
     * Field description after input element
     */
    protected string|Element $desc_after_content;

    /**
     * @param string $name must be exactly in `underscore_case`. Any other styles will be converted to that case
     * @param Form $form fields' parent form object
     * @param string $type fields' input type. Default is `text`
     */
    public function __construct(
        string $name,
        protected Form $form,
        protected string $type = 'text'
    ) {
        $this->name = underscoreCase($name);
        $this->id = $this->form()->id() . '--' . kebabCase($this->name());
    }

    public function __toString()
    {
        return (string)$this->render();
    }

    protected function target(): Element
    {
        return $this->input();
    }

    public function input(): Element
    {
        if (!isset($this->input)) {
            $this->input = new Element('input');
        }
        return $this->input;
    }

    protected function inputLine(): Element
    {
        if (!isset($this->input_line)) {
            $this->input_line = new Element;
        }
        return $this->input_line;
    }

    public function label(): Element
    {
        if (!isset($this->label)) {
            $this->label = new Element('label');
        }
        return $this->label;
    }

    public function form(): Form
    {
        return $this->form;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;
        if ($this->isRendered()) {
            if ($this->required && !$required) {
                $this->input()->wrapper()->unsetAttribute('required');
            } else if ($required) {
                $this->input()->setAttr('required');
            }
        }
        return $this;
    }

    public function setLabel(string|Element $content): self
    {
        $this->label_content = $content;
        if ($this->isRendered() && !$this->isInputInLabel()) {
            $this->label()->setContent($content);
        } else if ($this->isRendered()) {
            $this->refreshRender();
        }
        return $this;
    }

    public function labelContent(): Element|string|null
    {
        return $this->label_content ?? null;
    }

    public function unsetLabel(): self
    {
        unset($this->label_content);
        $this->refreshRender();
        return $this;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        if ($this->is('password')) {
            $this->required();
        }
        $this->refreshRender();
        return $this;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function setValue(?string $value): self
    {
        if (empty($value)) {
            unset($this->value);
        } else {
            $this->value = $value;
        }
        if (!$this->isRendered()) {
            return $this;
        }
        if (empty($value)) {
            if ($this->isTextarea()) {
                $this->input()->unsetContent();
            } else {
                $this->input()->wrapper()->unsetAttribute('value');
            }
        } else {
            if ($this->isTextarea()) {
                $this->input()->setContent($value);
            } else {
                $this->input()->setAttr('value', $value);
            }
        }
        return $this;
    }

    public function value(): ?string
    {
        return $this->value ?? null;
    }

    public function setPlaceholder(?string $content): self
    {
        if (empty($content)) {
            unset($this->attributes['placeholder']);
        } else {
            $this->attributes['placeholder'] = $content;
        }
        if ($this->isRendered() && empty($content)) {
            $this->input()->wrapper()->unsetAttribute('placeholder');
        } else if ($this->isRendered()) {
            $this->input()->setAttr('placeholder', $content);
        }
        return $this;
    }

    public function setOrder(
        #[ExpectedValues(
            self::ORDER_AFTER_LABEL,
            self::ORDER_BEFORE_LABEL,
            self::ORDER_AFTER_IN_LABEL,
            self::ORDER_BEFORE_IN_LABEL
        )] int $order
    ): self {
        $this->order = $order;
        $this->refreshRender();
        return $this;
    }

    public function order(): int
    {
        return $this->order;
    }

    public function useWrapper(bool $use): self
    {
        $this->use_wrapper = $use;
        if ($this->isRendered() && $use) {
            $this->template()->wrapper()->show();
        } else if ($this->isRendered()) {
            $this->template()->wrapper()->hide();
        }
        return $this;
    }

    public function inlineLabel(bool $inline_statement): self
    {
        $old_statement = $this->inline_label;
        $this->inline_label = $inline_statement;
        if ($old_statement !== $inline_statement) {
            $this->refreshRender();
        }
        return $this;
    }

    public function setPrefix(?string $prefix): self
    {
        if (!$prefix) {
            unset($this->prefix);
        } else {
            $this->prefix = $prefix;
        }
        $this->refreshRender();
        return $this;
    }

    public function setSuffix(?string $suffix): self
    {
        if (!$suffix) {
            unset($this->suffix);
        } else {
            $this->suffix = $suffix;
        }
        $this->refreshRender();
        return $this;
    }

    public function prependDescription(string|Element $description): self
    {
        $this->desc_before_content = $description;
        $this->refreshRender();
        return $this;
    }

    public function appendDescription(string|Element $description): self
    {
        $this->desc_after_content = $description;
        $this->refreshRender();
        return $this;
    }

    public function unsetDescriptionBefore(): self
    {
        unset($this->desc_before_content);
        $this->refreshRender();
        return $this;
    }

    public function unsetDescriptionAfter(): self
    {
        unset($this->desc_after_content);
        $this->refreshRender();
        return $this;
    }

    /**
     * @param string $key indicates for which field child element add class
     */
    public function addClass(
        string|array $classlist,
        #[ExpectedValues('wrapper', 'label', 'input')]
        string $key = 'input'
    ): self {
        if (is_string($classlist)) {
            $classlist = preg_split('/[\s\,]+/', $classlist);
        }
        $this->classlist[$key] ??= [];
        $new_classlist = [];
        foreach ($classlist as $class) {
            $class = normalizeClassname($class);
            if ($class && !in_array($class, $this->classlist[$key])) {
                array_push($this->classlist[$key], $class);
                $new_classlist[] = $class;
            }
        }
        // set new classlist to the rendered elements
        if ($this->isRendered() && !empty($new_classlist)) {
            switch ($key) {
                case 'wrapper':
                    $this->template()->addClass($new_classlist);
                    break;
                case 'label':
                    $this->label()->addClass($new_classlist);
                    break;
                case 'input':
                    $this->input()->addClass($new_classlist);
                    break;
            }
        }
        return $this;
    }

    public function addClassWrapper(string|array $classlist): self
    {
        $this->addClass($classlist, 'wrapper');
        return $this;
    }

    public function addClassLabel(string|array $classlist): self
    {
        $this->addClass($classlist, 'label');
        return $this;
    }

    public function addClassInput(string|array $classlist): self
    {
        $this->addClass($classlist, 'input');
        return $this;
    }

    // ==================================================================================
    // --------------------------------- STATEMENTS -------------------------------------
    // ----------------------------------------------------------------------------------

    public function is(string $type): bool
    {
        return $this->type() === $type;
    }

    public function isHidden(): bool
    {
        return $this->is('hidden');
    }

    public function isTextarea(): bool
    {
        return $this->is('textarea');
    }

    public function isRequired(): bool
    {
        return $this->required ?? false;
    }

    protected function isInputHasName(): bool
    {
        return !($this->is('submit') || $this->is('reset'));
    }

    protected function isInputInLabel(): bool
    {
        return in_array($this->order, [self::ORDER_AFTER_IN_LABEL, self::ORDER_BEFORE_IN_LABEL]);
    }

    protected function isInputFirst(): bool
    {
        return in_array($this->order, [self::ORDER_BEFORE_LABEL, self::ORDER_BEFORE_IN_LABEL]);
    }

    // ----------------------------------------------------------------------------------
    // --------------------------------- STATEMENTS -------------------------------------
    // ==================================================================================



    // ==================================================================================
    // ----------------------------------- HELPERS --------------------------------------
    // ----------------------------------------------------------------------------------

    protected function getClassMod(): ?string
    {
        if (
            !isset($this->default_class_mod)
            && in_array($this->type(), self::DEFAULT_BEM_MODS)
        ) {
            $this->default_class_mod = $this->type();
        }
        return parent::getClassMod();
    }

    protected function getTypeClassMod(): ?string
    {
        if ($mod = $this->default_class_mod ?? false) {
            return bemmod($mod);
        } else if (in_array($this->type(), self::DEFAULT_BEM_ELEMENTS)) {
            return bemmod($this->type());
        }
        return null;
    }

    // ----------------------------------------------------------------------------------
    // ----------------------------------- HELPERS --------------------------------------
    // ==================================================================================


    
    // ==================================================================================
    // -------------------------------- RENDER LOGIC ------------------------------------
    // ----------------------------------------------------------------------------------

    public function refreshRender(): void
    {
        if (!$this->isRendered()) {
            return;
        }
        $this->template = new Element;
        $this->input = new Element('input');
        $this->input_line = new Element;
        $this->label = new Element('label');
        $this->statement_render = false;
    }

    public function render(): Element
    {
        if ($this->isRendered()) {
            $this->template();
        }
        $this->prepareInput();
        $this->prepareWrapper();
        if (!$this->isHidden()) {
            switch (true) {
                case ($this->inline_label):
                    $this->renderInlineLabel();
                    break;
                case ($this->isInputInLabel()):
                    $this->renderInputInLabel();
                    break;
                default:
                    $this->renderDefault();
                    break;
            }
        } else {
            $this->template()->addContent(
                $this->input()
            );
        }
        $this->statement_render = true;
        return $this->template();
    }

    /**
     * Case for rendering field when label must be is in one line with input element
     * 
     * FIELD
     * - DESCRIPTION-BEFORE
     * - INPUT-LINE
     * - - LABEL [if order_before]
     * - - PREFIX
     * - - INPUT
     * - - SUFFIX
     * - - LABEL [if order_after]
     * - DESCRIPTION-AFTER
     */
    protected function renderInlineLabel(): void
    {
        // prepare label element with attributes
        $this->prepareLabel();
        // place label content in label element if it exists
        $this->prepareLabelContent();
        /** @var bool $line_required statement for input line element if it must be rendered */
        $line_required = false;
        // place label element in input line before in order input element
        if (!$this->isInputFirst() && !empty($this->labelContent())) {
            $this->inputLine()->addContent(
                $this->label()
            );
            $line_required = true;
        }
        // place prefix, input and suffix elements in input line
        $line_required = $this->renderInputLine() ? true : $line_required;
        // place label element in input line in order after input element 
        if ($this->isInputFirst() && !empty($this->labelContent())) {
            $this->inputLine()->addContent(
                $this->label()
            );
            $line_required = true;
        }
        // prepare input line element with attributes if it required
        $this->prepareInputLine($line_required);
        // prepare and place description before input element in parent element if exists
        $this->prepareDesctiptionBefore();
        // place input line in parent element
        $this->template()->addContent($this->inputLine());
        // prepare and place description after input element in parent element if exists
        $this->prepareDesctiptionAfter();
    }

    /**
     * Case for rendering field when input element must be inside of label element
     * 
     * FIELD
     * - DESCRIPTION-BEFORE
     * - LABEL
     * - - LABEL-CONTENT [if order_before]
     * - - INPUT-LINE
     * - - - PREFIX
     * - - - INPUT
     * - - - SUFFIX
     * - - LABEL-CONTENT [if order_after]
     * - DESCRIPTION-AFTER
     */
    protected function renderInputInLabel(): void
    {
        // prepare label element with attributes
        $this->prepareLabel();
        // place label content if it exists before input line element in label element
        if (!$this->isInputFirst()) {
            $this->prepareLabelContent();
        }
        /**
         * prepare and place prefix, input and suffix elements in input line element
         * 
         * @var bool $line_required statement for input line element if it must be rendered
         */
        $line_required = $this->renderInputLine();
        // prepare input line element with attributes if its required
        $this->prepareInputLine($line_required);
        // place input line element in label element
        $this->label()->addContent(
            $this->inputLine()
        );
        // place label content if it exists after input line element in label element
        if ($this->isInputFirst()) {
            $this->prepareLabelContent();
        }
        // prepare and place description before input element in parent element if exists
        $this->prepareDesctiptionBefore();
        // place label element in parent element
        $this->template()->addContent($this->label());
        // prepare and place description after input element in parent element if exists
        $this->prepareDesctiptionAfter();
    }

    /**
     * Case for rendering field with default structure
     * 
     * FIELD
     * - LABEL [if order_before]
     * - DESCRIPTION-BEFORE
     * - INPUT-LINE
     * - - PREFIX
     * - - INPUT
     * - - SUFFIX
     * - LABEL [if order_after]
     * - DESCRIPTION-AFTER
     */
    protected function renderDefault(): void
    {
        // prepare and place label element if its not empty in parent element
        if (!empty($this->label_content)) {
            $this->prepareLabel();
            $this->prepareLabelContent();
            $this->template()->addContent($this->label());
        }
        // prepare and place description before element if its not empty in parent element
        $this->prepareDesctiptionBefore();
        /**
         * prepare and place prefix, input and suffix elements in input line element
         * 
         * @var bool $line_required statement for input line element if it must be rendered
         */
        $line_required = $this->renderInputLine();
        // prepare input line element with attributes if its required
        $this->prepareInputLine($line_required);
        // place input line element in parent element
        $this->template()->addContent(
            $this->inputLine()
        );
        // prepare and place description after element if its not empty in parent element
        $this->prepareDesctiptionAfter();
    }

    protected function renderInputLine(): bool
    {
        $line_required = $this->renderInputAffix('prefix');
        $this->inputLine()->addContent(
            $this->input()
        );
        $line_required = $this->renderInputAffix('suffix') ? true : $line_required;
        if ($this->is('checkbox') || $this->is('password')) {
            $bem_element = 'cb-switch';
            $line_required = true;
            $switcher = new Element('span');
            if ($this->is('password')) {
                $bem_element = 'pw-switch';
                $switcher->setContent(getsvg('/images/icons/lamp.svg'));
            }
            $switcher->addClass(
                $this->form()->getChildClass($bem_element)
            );
            $this->inputLine()->addContent($switcher);
        }
        return $line_required;
    }

    protected function renderInputAffix(
        #[ExpectedValues('prefix', 'suffix')]
        string $affix_name
    ): bool {
        if ($affix = $this->getInputAffix($affix_name)) {
            $this->inputLine()->addContent($affix);
            return true;
        }
        return false;
    }

    protected function getInputAffix(
        #[ExpectedValues('prefix', 'suffix')]
        string $affix_name
    ): ?Element {
        if (empty($this->{$affix_name} ?? null)) {
            return null;
        }
        $affix = new Element('span');
        $affix->addClass(
            $this->form()->getChildClass(
                $affix_name, 
                $this->getTypeClassMod()
            )
        );
        $affix->setContent($this->{$affix_name});
        return $affix;
    }

    protected function prepareInput(): void
    {
        if ($this->isHidden()) {
            $this->input()->setAttr('type', $this->type());
            $this->input()->setAttr('name', $this->name());
            $this->input()->setAttr('value', $this->value());
            return;
        }
        if (
            empty($this->value())
            && old($this->name())
            && !$this->is('password')
        ) {
            $this->setValue(
                old($this->name())
            );
        }
        $attributes = ['id' => $this->id()];
        if ($this->isInputHasName()) {
            $attributes['name'] = $this->name();
        }
        if (!$this->isTextarea()) {
            $attributes['type'] = $this->type();
            if ($this->value()) {
                $attributes['value'] = $this->value();
            }
        } else {
            $this->input()->wrapper()->set('textarea');
            if (!empty($this->value())) {
                $this->input()->setContent($this->value());
            }
        }
        if ($this->isRequired()) {
            $attributes['required'] = null;
        }
        $attributes = array_merge($this->attributes, $attributes);
        foreach ($attributes as $name => $value) {
            $this->input()->setAttr($name, $value);
        }
        $classlist = $this->classlist['input'] ?? [];
        if ($this->use_default_class) {
            $bem_element = in_array($this->type(), self::DEFAULT_BEM_ELEMENTS) ? $this->type() : 'input';
            $bem_element = bemelem($bem_element);
            $default_classlist = $this->form()->getChildClass($bem_element, $this->getClassMod());
            $classlist = array_merge($classlist, $default_classlist);
        }
        $this->input()->addClass($classlist);
    }

    protected function prepareWrapper(): void
    {
        if (!$this->use_wrapper || $this->isHidden()) {
            $this->template()->wrapper()->hide();
            return;
        }
        $classlist = $this->classlist['wrapper'] ?? [];
        if ($this->use_default_class) {
            $default_classlist = $this->form()->getChildClass('field', $this->getTypeClassMod());
            $classlist = array_merge($classlist, $default_classlist);
        }
        if (!empty($classlist)) {
            $this->template()->addClass($classlist);
        }
    }

    protected function prepareLabel(): void
    {
        if (
            empty($this->labelContent())
            && (!$this->isInputInLabel() || $this->inline_label)
        ) {
            $this->label()->wrapper()->hide();
            return;
        }
        $this->label()->setAttr('for', $this->id());
        $classlist = $this->classlist['label'] ?? [];
        if ($this->use_default_class) {
            $default_classlist = $this->form()->getChildClass('label', $this->getTypeClassMod());
            $classlist = array_merge($classlist, $default_classlist);
        }
        if (!empty($classlist)) {
            $this->label()->addClass($classlist);
        }
    }

    protected function prepareLabelContent(): void
    {
        $content = $this->label_content ?? null;
        if (empty($content)) {
            return;
        } else if ($this->isInputInLabel()) {
            $content = new Element('span');
            $content->setContent($this->label_content);
        }
        $this->label()->addContent($content);
    }

    protected function prepareInputLine(bool $line_required): void
    {
        if (!$line_required) {
            $this->inputLine()->wrapper()->hide();
            return;
        }
        $this->inputLine()->addClass(
            $this->form()->getChildClass('line', $this->getTypeClassMod())
        );
        if ($this->isInputInLabel()) {
            $this->inputLine()->wrapper()->set('span');
        }
    }

    protected function prepareDesctiptionBefore(): void
    {
        if (empty($this->desc_before_content ?? null)) {
            return;
        }
        $desc = new Element('p');
        $desc->addClass(
            $this->form()->getChildClass('description', $this->getTypeClassMod())
        );
        $desc->setContent($this->desc_before_content);
        $this->template()->addContent($desc);
    }

    protected function prepareDesctiptionAfter(): void
    {
        if (empty($this->desc_after_content ?? null)) {
            return;
        }
        $desc = new Element('p');
        $desc->addClass(
            $this->form()->getChildClass('annotation', $this->getTypeClassMod())
        );
        $desc->setContent($this->desc_after_content);
        $this->template()->addContent($desc);
    }
    
    // ----------------------------------------------------------------------------------
    // --------------------------------- RENDER LOGIC -----------------------------------
    // ==================================================================================
}
