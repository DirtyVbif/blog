<?php

namespace Blog\Modules\Template;

interface TemplateInterface
{
    /**
     * Render template to string or markup rendered string for twig as safe content
     */
    public function render(): string|\Twig\Markup;

    /**
     * Set template to be marked up for twig as safe or not
     */
    public function setMarkup(bool $statement = true): self;

    /**
     * Set template statement to be rendered or not
     * 
     * By default template would't be renderable if it's empty.
     * Template is empty if it has no content and wasn't changed.
     */
    public function setRenderable(bool $statement): self;

    /**
     * Set template name. There is no need to specify template extension. `.html.twig` extansion will be used automatically
     */
    public function setName(string $template_name): self;

    /**
     * Set twig namespace for template thas will be used for specified template name
     */
    public function setNamespace(string $namespace): self;

    /**
     * Set template to use specified twig namespace and template name.
     * 
     * It is an alias for @method setNamespace() and @method setName()
     * 
     * @param string $template_name name of template that must be rendered.
     * @param string $namespace twig defined namespace where specified template located
     * There is no need to specify template extension `.html.twig` it will be used automatically
     */
    public function use(string $template_name, string $namespace): self;

    /**
     * Set template variable by name or set variables from array
     * 
     * @param string $data template variable name
     * @param array $data array with variables where array key is template variable name
     * 
     * @param mixed $value [optional] variable value in case @param string $data provided
     */
    public function set(string|array $data, $value = null): self;

    /**
     * Get template variable by name if it is defined
     */
    public function get(string $name);

    /**
     * Check if template variable is defined
     */
    public function isset(string $name): bool;

    /**
     * Get template defined variables
     */
    public function data(): array;
}
