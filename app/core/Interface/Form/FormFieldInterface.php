<?php

namespace Blog\Interface\Form;

use Blog\Modules\Template\Element;

interface FormFieldInterface
{
    /**
     * Get field parent form object
     */
    public function form(): Form;

    /**
     * Get name of field
     */
    public function name(): string;

    /**
     * Get field automatically generated id attribute value based on form id
     */
    public function id(): string;

    /**
     * Get field label template element
     */
    public function label(): Element;

    /**
     * Set field label content
     */
    public function setLabel(string|Element $content): self;

    /**
     * Get field label content if it was defined
     */
    public function labelContent(): Element|string|null;

    /**
     * Uset field label
     */
    public function unsetLabel(): self;

    /**
     * Set field input type
     * 
     * @param string $type to set field type
     * @param null $type to unset field value
     */
    public function setType(?string $type): self;

    /**
     * Get field input type
     */
    public function type(): string;

    /**
     * Set field value
     * 
     * @param string $value to set field value
     * @param null $value to unset field value
     */
    public function setValue(?string $value): self;

    /**
     * Get field value if it was defined or null
     */
    public function value(): ?string;

    /**
     * Set input placeholder content
     */
    public function setPlaceholder(?string $content): self;

    /**
     * Set field and field label order
     * 
     * @param int $order available values:
     * * @var `FormField::ORDER_AFTER_LABEL (0)` - default order, label above (before) input field;
     * * @var `FormField::ORDER_BEFORE_LABEL (1)` - label under (after) input field;
     * * @var `FormField::ORDER_IN_LABEL_AFTER (2)` - input field inside label after label content;
     * * @var `FormField::ORDER_IN_LABEL_BEFORE (3)` - input field inside label before label content;
     */
    public function setOrder(int $order): self;

    /**
     * Get current order id. For description @see @method `self::setOrder()`
     */
    public function order(): int;

    /**
     * Set statement for field and label parent wrapper element
     */
    public function useWrapper(bool $use): self;

    /**
     * Get field parent wrapper element template
     */
    public function template(): Element;

    /**
     * Set label in one line with field input or not. Corresponds with field and label order.
     */
    public function inlineLabel(bool $inline_statement): self;

    /**
     * Set field input prefix content
     * 
     * @param string $prefix new content for field prefix
     * @param null $prefix to unset field prefix
     */
    public function setPrefix(?string $prefix): self;

    /**
     * Set field input suffix content
     * 
     * @param string $suffix new content for field suffix
     * @param null $suffix to unset field suffix
     */
    public function setSuffix(?string $suffix): self;

    /**
     * Add some description before input field
     */
    public function prependDescription(string|Element $description): self;

    /**
     * Add some description after input field
     */
    public function appendDescription(string|Element $description): self;

    /**
     * Unset description content that is before input element
     */
    public function unsetDescriptionBefore(): self;

    /**
     * Unset description content that is after input element
     */
    public function unsetDescriptionAfter(): self;

    /**
     * Get input template element
     */
    public function input(): Element;

    /**
     * Set field input required statement. By default field is not required.
     */
    public function required(bool $required = true): self;

    /**
     * Check field if it's input required or not
     */
    public function isRequired(): bool;

    /**
     * Build form field template correspondes with current field configuration and content
     */
    public function render(): Element;

    /**
     * Set statement for using or not default form classes
     */
    public function useDefaultClass(bool $use): self;

    /**
     * Add custom classes to field parent wrapper element
     * 
     * @param string|string[] $classlist
     */
    public function addWrapperClass(string|array $classlist): self;

    /**
     * Alias method for @method addWrapperClass()
     */
    public function clsW(string|array $classlist): self;

    /**
     * Add custom classes to field label element
     * 
     * @param string|string[] $classlist
     */
    public function addLabelClass(string|array $classlist): self;

    /**
     * Alias method for @method addWLabelClass()
     */
    public function clsL(string|array $classlist): self;

    /**
     * Add custom classes to field input element
     * 
     * @param string|string[] $classlist
     */
    public function addInputClass(string|array $classlist): self;

    /**
     * Alias method for @method addInputClass()
     */
    public function clsI(string|array $classlist): self;

    /**
     * Add BEM-model modificator to default form template class
     * 
     * BEM-model modificator can be provided without `_` underscore prefix. It will be added automatically.
     */
    public function setClassMod(?string $mod): self;

    /**
     * Check if field is of type
     */
    public function is(string $type): bool;

    /**
     * Check if field is of type `hidden`
     * 
     * Alias method for @method is() with `hidden` argument
     */
    public function isHidden(): bool;

    /**
     * Check if field is of type `textarea`
     * 
     * Alias method for @method is() with `textarea` argument
     */
    public function isTextarea(): bool;
}
