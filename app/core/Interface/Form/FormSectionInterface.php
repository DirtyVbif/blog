<?php

namespace Blog\Interface\Form;

use Blog\Modules\Template\Element;
use Blog\Modules\TemplateFacade\Title;

interface FormSectionInterface
{
    /**
     * Get access to section parent form object
     */
    public function form(): Form;

    /**
     * Get section name
     */
    public function name(): string;

    /**
     * Get section title element
     */
    public function title(): Title;

    /**
     * Set new content to title element
     * 
     * @param string|Element $content new title element content to set
     * @param null $content to unset current title
     */
    public function setTitle(string|Element|null $content): self;

    /**
     * Alias for @method setTitle() with `NULL` argument
     */
    public function unsetTitle(): self;

    /**
     * @param int $size @see Blog\Modules\TemplateFacade\Title::size()
     */
    public function setTitleSize(int $size): self;

    /**
     * Set specific HTML tag name for section element
     */
    public function setTag(string $tag): self;
}
