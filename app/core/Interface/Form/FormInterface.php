<?php

namespace Blog\Interface\Form;

use JetBrains\PhpStorm\ExpectedValues;

interface FormInterface
{
    /**
     * Add new class or classes to form template classlist
     */
    public function addClass(string|array $classlist): self;

    /**
     * Add BEM-model modificator to default form template class
     * 
     * BEM-model modificator can be provided without `_` underscore prefix. It will be added automatically.
     */
    public function setClassMod(?string $mod): self;

    /**
     * Set form statement to use or not to use default form template class
     */
    public function useDefaultClass(bool $use): self;

    /**
     * Set new status for using custom classlist in generating classes for nested form items
     */
    public function useClasslistForNestedItems(bool $use): self;

    /**
     * Get current form classlist.
     * 
     * Result consideres using of default form class and default class modificator
     * 
     * @return array classlist if `@param bool $return_string` is `FALSE`
     * @return string classlist if `@param bool $return_string` is `TRUE`
     */
    public function getClasslist(bool $return_string = false): array|string;

    /**
     * Generate classlist for item based on item name and form classlist
     * 
     * @param string $item_name it will be added as classname suffix as BEM-model element to base form classlist
     * 
     * @return array classlist if `@param bool $return_string` is `FALSE`
     * @return string classlist if `@param bool $return_string` is `TRUE`
     */
    public function getItemClasslist(string $item_name, bool $return_string = false): array|string;

    /**
     * Set form name that will be used in automatically generated form and form elements ids and names
     */
    public function setName(?string $form_name): void;

    /**
     * Get current form name if it was specified or null if not
     */
    public function name(): ?string;

    /**
     * Set new form mask for autogenerateble template id attribute based on form name
     * 
     * Mask string must be compatable with `sprintf()` function. By default form uses `form-%s` mask.
     */
    public function setFormIdMask(string $mask_string): void;

    /**
     * Set form custom template id attribute value
     */
    public function setId(string $form_id): void;
    /**
     * Get current form template id attribute value string based on form name or null if form name wasn't specified
     */
    public function id(): ?string;

    /**
     * Set form data send method `GET | POST | PUT | PATCH | DELETE`
     */
    public function setMethod(
        #[ExpectedValues(values: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])]
        string $method
    ): void;

    /**
     * Get current form data send method
     */
    public function method(): string;

    /**
     * Set form data send path
     */
    public function setAction(string $path): void;

    /**
     * Get current form data send path
     */
    public function action(): string;

    /**
     * Define new form field
     * 
     * Field can be defined into existing form section
     * 
     * @return FormFieldInterface defined form field
     */
    public function setField(string $name, ?string $section = null): FormFieldInterface;

    /**
     * Attach defined field into existing section
     */
    public function setFieldSection(string $name, ?string $section = null): void;

    /**
     * Generate array tree with fields inside of sections
     */
    public function getFieldOrder(): array;

    /**
     * Get access to specified form field
     * 
     * @return FormFieldInterface
     * @return null if field doesn't defined
     */
    public function field(string $name): ?FormFieldInterface;

    /**
     * Define new form section
     * 
     * Section position can be manipulated with @param int $order
     * 
     * @return FormSectionInterface defined form section
     */
    public function setSection(string $name, int $order = 0): FormSectionInterface;

    /**
     * Set sorting order for defined section by section name
     */
    public function setSectionOrder(string $name, int $order = 0): void;

    /**
     * Generate array tree with sections order
     */
    public function getSectionOrder(): array;

    /**
     * Get access to specified form section
     * 
     * @return FormSectionInterface
     * @return null if section doesn't defined
     */
    public function section(string $name): ?FormSectionInterface;
}
