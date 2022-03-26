<?php

namespace Blog\Interface\Form;

use Blog\Interface\TemplateInterface;
use Blog\Modules\Template\Element;
use JetBrains\PhpStorm\ExpectedValues;

class FormField implements FormFieldInterface, TemplateInterface
{
    public const ORDER_AFTER_LABEL = 0;
    public const ORDER_BEFORE_LABEL = 1;
    public const ORDER_IN_LABEL_BEFORE = 2;
    public const ORDER_IN_LABEL_AFTER = 3;
    public const DEFAULT_BEM_MODS = [
        'checkbox', 'textarea', 'reset', 'submit'
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
     * Field parent wrapper element
     */
    protected Element $template;

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
    protected string|Element $description_before;

    /**
     * Field description after input element
     */
    protected string|Element $description_after;

    /**
     * Pool with custom classes for field wrapper, label and input elements.
     * 
     * Each element has it's own stack named array key: `wrapper`, `label`, `input`
     */
    protected array $classlist = [];

    /**
     * BEM-model element modificator for classlist
     */
    protected string $class_mod;

    /**
     * Statement of using default form classlist
     */
    protected bool $use_default_class = true;

    /**
     * @property array<string, bool> $prepared_element array with prepared to render elements
     */
    protected array $prepared_elements = [];

    /**
     * array with attributes for input field
     */
    protected array $attributes = [];

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

    public function template(): Element
    {
        if (!isset($this->template)) {
            $this->template = new Element;
        }
        return $this->template;
    }

    public function render(): Element
    {
        $this->prepareInput();
        if (!$this->isHidden()) {
            $this->prepareInputLine();
            $this->prepareLabel();
        }
        $this->prepareWrapper();
        return $this->template();
    }

    protected function elementPrepared(string $name): bool
    {
        if (!empty($this->prepared_elements[$name] ?? null)) {
            return true;
        }
        $this->prepared_elements[$name] = true;
        return false;
    }

    protected function prepareInput(): void
    {
        if ($this->elementPrepared('input')) {
            return;
        }
        $attributes = [];
        if ($this->isNameRequired()) {
            $attributes['name'] = $this->name();
        }
        if (!$this->isHidden()) {
            $attributes['id'] = $this->id();
            $classlist = $this->form()->getItemClasslist($this->getInputElementClass(), $this->getClasslistMod());
            if (!empty($this->classlist['input'] ?? null)) {
                $classlist = array_merge($classlist, $this->classlist['input']);
            }
            $this->input()->addClass($classlist);
        }
        $attributes = array_merge($this->attributes, $attributes);
        foreach ($attributes as $name => $value) {
            $this->input()->setAttr($name, $value);
        }
        if ($this->isRequired()) {
            $this->input()->setAttr('required');
        }
        $this->prepareInputType();
        $this->prepareInputValue();
    }

    protected function getInputElementClass(): string
    {
        $element = in_array($this->type(), self::DEFAULT_BEM_MODS) ? $this->type() :'input';
        return bemelem($element);
    }

    protected function isNameRequired(): bool
    {
        return !($this->is('submit') || $this->is('reset'));
    }

    protected function prepareInputType(): void
    {
        if ($this->isTextarea()) {
            $this->input()->wrapper()->set('textarea');
        } else {
            $this->input()->setAttr('type', $this->type());
        }
    }

    protected function prepareInputValue(): void
    {
        if (empty($this->value())) {
            return;
        } else if ($this->isTextarea()) {
            $this->input()->setContent($this->value());
        } else {
            $this->input()->setAttr('value', $this->value());
        }
    }

    protected function prepareInputLine(): void
    {
        if ($this->elementPrepared('input-line')) {
            return;
        }
        if ($this->inline_label) {
            if ($this->order === self::ORDER_BEFORE_LABEL) {
                $this->prepareInputAffixes();
            }
            if ($this->label_content ?? false) {
                $this->prepareLabel();
                $this->inputLine()->content()->add(
                    $this->labelElement()
                );
            }
        }
        $this->prepareInputAffixes();
    }

    protected function prepareInputAffixes(): void
    {
        if ($this->elementPrepared('input-affixes')) {
            return;
        }
        if (!empty($this->prefix ?? null)) {
            $prefix = new Element('span');
            $prefix->addClass(
                $this->form()->getItemClasslist(
                    'prefix',
                    $this->getClasslistMod()
                )
            );
            $prefix->setContent($this->prefix);
            $this->inputLine()->content()->add($prefix);
        }
        $this->inputLine()->content()->add(
            $this->input()
        );
        if (!empty($this->suffix ?? null)) {
            $suffix = new Element('span');
            $suffix->addClass(
                $this->form()->getItemClasslist(
                    'suffix',
                    $this->getClasslistMod()
                )
            );
            $suffix->setContent($this->suffix);
            $this->inputLine()->content()->add($suffix);
        }
        if (
            !isset($this->prefix, $this->suffix)
            && (!$this->inline_label || !isset($this->label_content))
        ) {
            $this->inputLine()->wrapper()->hide();
        } else {
            $this->inputLine()->addClass(
                $this->form()->getItemClasslist(
                    'line',
                    $this->getClasslistMod()
                )
            );
        }
    }

    protected function prepareLabel(): void
    {
        if ($this->elementPrepared('label')) {
            return;
        }
        $this->labelElement()->setAttr('for', $this->id());
        if ($this->order === self::ORDER_IN_LABEL_BEFORE) {
            $this->labelElement()->content()->add(
                $this->inputLine()
            );
        }
        if (!empty($this->label_content ?? null)) {
            $this->labelElement()->content()->add(
                $this->label_content
            );
        }
        if ($this->order === self::ORDER_IN_LABEL_AFTER) {
            $this->labelElement()->content()->add(
                $this->inputLine()
            );
        }
        if (
            empty($this->label_content ?? null)
            && $this->order < 2
        ) {
            $this->labelElement()->wrapper()->hide();
            return;
        }
        $this->labelElement()->addClass(
            $this->form()->getItemClasslist(
                'label',
                $this->getClasslistMod()
            )
        );
        if (!empty($this->classlist['label'])) {
            $this->labelElement()->addClass(
                $this->classlist['label']
            );
        }
    }

    protected function prepareWrapper(): void
    {
        if ($this->elementPrepared('wrapper')) {
            return;
        } else if (!$this->use_wrapper || $this->isHidden()) {
            $this->template()->wrapper()->hide();
        } else {
            $this->template()->addClass(
                $this->form()->getItemClasslist('field', $this->getClasslistMod())
            );
            if (!empty($this->classlist['wrapper'])) {
                $this->template()->addClass(
                    $this->classlist['wrapper']
                );
            }
        }
        if (
            $this->inline_label
            || $this->order === self::ORDER_BEFORE_LABEL
            || $this->isHidden()
        ) {
            $this->prepareInputInWrapper();
        }
        if (!$this->inline_label && !$this->isHidden()) {
            $this->template()->content()->add(
                $this->labelElement()
            );
            if ($this->order !== self::ORDER_BEFORE_LABEL) {
                $this->prepareInputInWrapper();
            }
        }
    }

    protected function prepareInputInWrapper(): void
    {
        if ($this->elementPrepared('input-in-wrapper')) {
            return;
        } else if ($this->isHidden()) {
            $this->template()->content()->add(
                $this->input()
            );
            return;
        }
        $this->prepareDesctiptionBefore();
        $this->template()->content()->add(
            $this->inputLine()
        );
        $this->prepareDesctiptionAfter();
    }

    protected function prepareDesctiptionBefore(): void
    {
        if (
            $this->elementPrepared('description-before')
            || empty($this->description_before ?? null)
        ) {
            return;
        }
        $desc = new Element('p');
        $desc->addClass(
            $this->form()->getItemClasslist('description', $this->getClasslistMod())
        );
        $desc->setContent($this->description_before);
        $this->template()->content()->add($desc);
    }

    protected function prepareDesctiptionAfter(): void
    {
        if (
            $this->elementPrepared('description-after')
            || empty($this->description_after ?? null)
        ) {
            return;
        }
        $desc = new Element('p');
        $desc->addClass(
            $this->form()->getItemClasslist('annotation', $this->getClasslistMod())
        );
        $desc->setContent($this->description_after);
        $this->template()->content()->add($desc);
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

    public function input(): Element
    {
        if (!isset($this->input)) {
            $this->input = new Element('input');
        }
        return $this->input;
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required ?? false;
    }

    protected function inputLine(): Element
    {
        if (!isset($this->input_line)) {
            $this->input_line = new Element;
        }
        return $this->input_line;
    }

    public function labelElement(): Element
    {
        if (!isset($this->label)) {
            $this->label = new Element('label');
        }
        return $this->label;
    }

    public function setLabel(string|Element $content): self
    {
        $this->label_content = $content;
        return $this;
    }

    public function label(): Element|string|null
    {
        return $this->label_content ?? null;
    }

    public function unsetLabel(): self
    {
        unset($this->label_content);
        return $this;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function setValue(?string $value): self
    {
        if (!$value) {
            unset($this->value);
        } else {
            $this->value = $value;
        }
        return $this;
    }

    public function value(): ?string
    {
        return $this->value ?? null;
    }

    public function setPlaceholder(?string $content): self
    {
        if (!$content) {
            unset($this->placeholder);
        } else {
            $this->placeholder = $content;
        }
        return $this;
    }

    protected function placeholder(): ?string
    {
        return $this->placeholder ?? null;
    }

    public function setOrder(
        #[ExpectedValues(
            self::ORDER_AFTER_LABEL,
            self::ORDER_BEFORE_LABEL,
            self::ORDER_IN_LABEL_AFTER,
            self::ORDER_IN_LABEL_BEFORE
        )] int $order
    ): self {
        $this->order = $order;
        return $this;
    }

    public function order(): int
    {
        return $this->order;
    }

    public function useWrapper(bool $use): self
    {
        $this->use_wrapper = $use;
        return $this;
    }

    public function inlineLabel(bool $inline_statement): self
    {
        $this->inline_label = $inline_statement;
        return $this;
    }

    public function setPrefix(?string $prefix): self
    {
        if (!$prefix) {
            unset($this->prefix);
        } else {
            $this->prefix = $prefix;
        }
        return $this;
    }

    public function setSuffix(?string $suffix): self
    {
        if (!$suffix) {
            unset($this->suffix);
        } else {
            $this->suffix = $suffix;
        }
        return $this;
    }

    public function prependDescription(string|Element $description): self
    {
        $this->description_before = $description;
        return $this;
    }

    public function appendDescription(string|Element $description): self
    {
        $this->description_after = $description;
        return $this;
    }

    public function unsetDescriptionBefore(): self
    {
        unset($this->description_before);
        return $this;
    }

    public function unsetDescriptionAfter(): self
    {
        unset($this->description_after);
        return $this;
    }

    /**
     * @param string $i indicator of classlist pool. For description @see @property $classlist
     */
    protected function addClass(string|array $classlist, string $i): void
    {
        if (is_string($classlist)) {
            $classlist = preg_split('/[\s\,]+/', $classlist);
        }
        $this->classlist[$i] ??= [];
        foreach ($classlist as $class) {
            $class = normalizeClassname($class);
            if ($class && !in_array($class, $this->classlist[$i])) {
                array_push($this->classlist[$i], $class);
            }
        }
    }

    protected function getClasslistMod(): ?string
    {
        if (
            !isset($this->class_mod)
            && in_array($this->type(), self::DEFAULT_BEM_MODS)
        ) {
            $this->class_mod = bemmod($this->type());
        }
        return $this->class_mod ?? null;
    }
    
    public function clsW(string|array $classlist): self
    {
        return $this->addWrapperClass($classlist);
    }

    public function addWrapperClass(string|array $classlist): self
    {
        $this->addClass($classlist, 'wrapper');
        return $this;
    }
    
    public function clsL(string|array $classlist): self
    {
        return $this->addWrapperClass($classlist);
    }

    public function addLabelClass(string|array $classlist): self
    {
        $this->addClass($classlist, 'label');
        return $this;
    }
    
    public function clsI(string|array $classlist): self
    {
        return $this->addInputClass($classlist);
    }

    public function addInputClass(string|array $classlist): self
    {
        $this->addClass($classlist, 'input');
        return $this;
    }

    public function setClassMod(?string $mod): self
    {
        if (!$mod) {
            unset($this->class_mod);
        } else {
            $this->class_mod = bemmod($mod);
        }
        return $this;
    }

    public function useDefaultClass(bool $use): self
    {
        $this->use_default_class = $use;
        return $this;
    }

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
}
