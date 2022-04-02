<?php

namespace Blog\Interface;

use Blog\Modules\Template\Element;

interface RenderableInterface
{
    /**
     * Rebuild template for new render in case on changes for rendered template
     */
    public function refreshRender(): void;

    /**
     * Main render method to build configured interface into render-ready template
     */
    public function render(): Element;
}
