<?php

namespace Blog\Interface;

use Blog\Modules\Template\Element;

interface TemplateInterface
{
    /**
     * Main render method to build configured interface into render-ready template
     */
    public function render(): Element;

    /**
     * Get access to interface template element
     */
    public function template(): Element;
    
    /**
     * Field method to set input element attributes
     * 
     * @param string $name attribute name
     * @param string $value [optional] set attribute value
     * @param null $value [optional] set attribute without value
     * @param bool $data_attribute would attribute have auto-prefix `data-` or not
     */
    public function setAttribute(string $name, ?string $value = null, bool $data_attribute = false): self;
}
