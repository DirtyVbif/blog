<?php

namespace Blog\Modules\Template;

interface TemplateWrapperInterface
{
    /**
     * Get access to the template wrapper element object
     */
    public function wrapper(): Wrapper;

    /**
     * Set template wrapper element attribute
     * 
     * @param string $data name of attribute to set for wrapper element
     * @param array<string, ?string> $data array with attributes to set where array key is the name of attribute
     * @param ?string $value [optional] attribute value only for case when attribute name provided as string
     * @param bool $data_attribute [optional] statement for `data-` attribute name prefix
     */
    public function setAttribute(string|array $data, ?string $value = null, bool $data_attribute = false): self;

    /**
     * Add classes to the template wrapper element
     * 
     * @param string|string[] $classlist
     */
    public function addClass(string|array $classlist): self;

    /**
     * Set id attribute for the template wrapper element
     */
    public function setId(string $id): self;
}
