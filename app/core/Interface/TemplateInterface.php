<?php

namespace Blog\Interface;

use Blog\Modules\Template\Element;

interface TemplateInterface
{
    /**
     * Get access to interface template element
     */
    public function template(): Element;
    
    /**
     * Set new attribute for element
     * 
     * @param string $name attribute name
     * @param string|null $value [optional] set HTML element attribute value or `NULL` to set attributes without value
     * @param bool $data_attribute statement for attribute to have auto-prefix `data-`
     */
    public function setAttribute(string $name, ?string $value = null, bool $data_attribute = false): self;

    /**
     * Add class to the temlate classlist
     */
    public function addClass(string|array $classlist): self;

    /**
     * Add BEM-model modificator to default classes
     * 
     * BEM-model modificator can be provided without `_` underscore prefix. It will be added automatically.
     * 
     * @param string $mod to set new modificator for classlist
     * @param null $mod to unset defined modificator
     */
    public function setDefaultClassMod(?string $mod): self;

    /**
     * Set element statement to use default classes
     */
    public function useDefaultClass(bool $use): self;
}
