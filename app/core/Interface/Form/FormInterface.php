<?php

namespace Blog\Interface\Form;

use Blog\Modules\TemplateFacade\Title;
use JetBrains\PhpStorm\ExpectedValues;

interface FormInterface
{
    /**
     * Set new status for using custom classlist in generating classes for nested form items
     */
    public function useCustomClassForChildren(bool $use): self;

    /**
     * Get form default classlist with BEM-model modificator if it specified
     */
    public function getDefaultClasslist(): array;
    
    /**
     * Get current form classlist.
     * 
     * Result considers using of default form class and default BEM-model modificator for default classes
     * 
     * @return array classlist if `@param bool $return_string` is `FALSE`
     * @return string classlist if `@param bool $return_string` is `TRUE`
     */
    public function getClasslist(bool $return_string = false): array|string;

    /**
     * Generate classlist for item based on item name and form classlist
     * 
     * @param string $bem_element it will be added as BEM-model `__element-name` to base form classlist
     * @param ?string $bem_element_mod [optional] it will be added to provied @param string $bem_element as BEM-model `_mod-name`
     * 
     * @return array classlist if `@param bool $return_string` is `FALSE`
     * @return string classlist if `@param bool $return_string` is `TRUE`
     */
    public function getChildClass(string $bem_element, ?string $bem_element_mod = null, bool $return_string = false): array|string;

    /**
     * Set form name that will be used in automatically generated form and form elements ids and names
     */
    public function setName(?string $form_name): void;

    /**
     * Get current form name if it was specified or null if not
     */
    public function name(): ?string;

    /**
     * Set form custom template id attribute value
     */
    public function setId(string $id): void;

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
     * @param string $name field name in `underscore_case` or it will be automatically formatted to it
     * @param string $type field input type
     * @param int $order [optional] field sorting order weight. @see @method setFieldOrder()
     * @param ?string $section [optional] name of section where to attach field. @see @method setFieldSection()
     * 
     * @return FormField defined form field
     */
    public function setField(string $name, string $type = 'text', int $order = 0, ?string $section = null): FormField;

    /**
     * Set form submit field
     * 
     * It is an alias for @method setField() with arguments `(name: 'submit', type: 'submit', order: @param int $order, section: @param ?string $section)`
     * 
     * To get access to that field use @method f() or field() with argument `(name: 'submit')`
     * 
     * @param int $order @see @method setField()
     * @param ?string $section @see @method setField()
     */
    public function setSubmit(int $order = 0, ?string $section = null): FormField;

    /**
     * Set form hidden field with field value
     * 
     * Is is an alias for @method setField() with arguments `(name: $name, type: 'hidden')`
     * with callback of field @method FOrmField::setValue() with argument `(value: $value)`
     * 
     * @param string $name hidden field attribute `name` value
     * @param string $value hidden field attribute `value` value
     * 
     * @return FormField created hidden field
     */
    public function set(string $name, string $value): FormField;

    /**
     * Attach defined field into existing section
     */
    public function setFieldSection(string $name, ?string $section = null): void;

    /**
     * Set field sorting order weight. By default all fields has weight `0`
     */
    public function setFieldOrder(string $name, int $order): void;

    /**
     * Generate array tree with fields inside of sections and fields order
     */
    public function getFieldsTree(): array;

    /**
     * Get fields that corresponds to the provided section name
     * 
     * @return FormField[]
     */
    public function getSectionFields(string $section_name): array;

    /**
     * Get access to specified form field
     * 
     * @return FormField
     * @return null if field doesn't defined
     */
    public function field(string $name): ?FormField;

    /**
     * Alias for @method field()
     */
    public function f(string $name): ?FormField;

    /**
     * Get form defined fields array
     * 
     * @return FormField[]
     */
    public function fields(): array;

    /**
     * Define new form section
     * 
     * Section position can be manipulated with @param int $order
     * 
     * @return FormSection defined form section
     */
    public function setSection(string $name, int $order = 0): FormSection;

    /**
     * Set sorting order for defined section by section name
     */
    public function setSectionOrder(string $name, int $order = 0): void;

    /**
     * Generate array tree with sections order
     */
    public function getSectionsOrder(): array;

    /**
     * Get access to specified form section
     * 
     * @return FormSection
     * @return null if section doesn't defined
     */
    public function section(string $name): ?FormSection;

    /**
     * Alias for @method section()
     */
    public function s(string $name): ?FormSection;

    /**
     * Get form defined sections array
     * 
     * @return FormSection[]
     */
    public function sections(): array;

    /**
     * Get defined form title template element or null if it is undefined
     * 
     * @return Title defined form title element
     * @return null if title is undefined
     */
    public function title(): ?Title;

    /**
     * Set form title content
     * 
     * @param string $content set new form title content
     * @param null $content to unset defined form title
     * 
     * @param int $size @see @method setTitleSize()
     */
    public function setTitle(?string $content, int $size = 2): void;

    /**
     * @param int $size @see Blog\Modules\TemplateFacade\Title::size()
     */
    public function setTitleSize(int $size): void;

    /**
     * Get current form title size
     */
    public function getTitleSize(): int;

    /**
     * Set statement of using form csrf protection
     */
    public function useCsrf(bool $use = true): void;

    /**
     * Check if form element contains specified field type
     */
    public function contains(string $field_type): bool;
}
