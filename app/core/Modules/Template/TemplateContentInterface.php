<?php

namespace Blog\Modules\Template;

interface TemplateContentInterface
{
    /**
     * Set new template content and overwrite existing content
     */
    public function setContent(string|RenderableElement $content): self;

    /**
     * Append new content for the template and specify content key for the access to it
     * 
     * @param string $key [optional] specify content key for the access to that content element
     */
    public function addContent(string|RenderableElement $content, ?string $key = null): self;

    /**
     * Get access to the template content element by it's key or index of key wasn't defined
     * 
     * @param string $key defined content key name
     * @param int $key content index if key for it wasn't defined
     * 
     * @return string|RenderableElement defined content element
     * @return null if no content for specified key
     */
    public function getContent(string|int $key): string|RenderableElement|null;

    /**
     * Unset content element by it's key or index
     */
    public function unsetContent(string|int $key): self;
}
