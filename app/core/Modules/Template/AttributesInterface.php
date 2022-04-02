<?php

namespace Blog\Modules\Template;

interface AttributesInterface
{
    /**
     * Output defined attributes as escaped for twig string
     */
    public function render(): ?\Twig\Markup;

    /**
     * Set template wrapper element attribute
     * 
     * @param string $data name of attribute to set for wrapper element
     * @param array<string, ?string> $data array with attributes to set where array key is the name of attribute
     * @param ?string $value [optional] attribute value only for case when attribute name provided as string
     * @param bool $data_attribute [optional] statement for `data-` attribute name prefix
     */
    public function set(string|array $data, ?string $value, bool $data_attribute): self;

    /**
     * Get attribute value by attribute name
     */
    public function get(string $name): ?string;

    /**
     * Unset specified attribute
     */
    public function unset(string $name): self;

    /**
     * Add classes
     * 
     * @param string|string[] $classlist
     */
    public function addClass(string|array $classlist): self;

    /**
     * Get element defined classes as array or as string
     */
    public function classlist(bool $return_as_string): array|string;

    /**
     * Set id attribute and check if that id was already used for another element
     */
    public function setId(string $id): self;
}
