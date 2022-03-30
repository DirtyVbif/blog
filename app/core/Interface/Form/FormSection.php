<?php

namespace Blog\Interface\Form;

use Blog\Modules\Template\Element;
use Blog\Modules\TemplateFacade\Title;

class FormSection extends AbstractFormElement implements FormSectionInterface
{
    /**
     * Section name in `kebab-case-style`
     */
    protected string $name;

    /**
     * Title element
     */
    protected Title $title;

    /**
     * Title element content
     */
    protected string|Element $title_content;

    /**
     * Size for @var Title $title title element
     */
    protected int $title_size;

    /**
     * Custom classlist
     */
    protected array $classlist = [];

    /**
     * BEM-model modificator for default parent form element classlist
     */
    protected string $classlist_mod;

    /**
     * Statement for using default classlist
     */
    protected bool $use_default_class = true;

    /**
     * @var array<string, ?string> $attributes array with section element custom attributes
     */
    protected array $attributes = [];

    /**
     * Current template render statement
     */
    protected bool $statement_render;

    /**
     * @param string $name must be exactly in `kebab-case-style`. Any other styles will be converted to that case
     * @param Form $form parent form object
     */
    public function __construct(
        string $name,
        protected Form $form
    ) {
        $this->name = kebabCase($name);
    }

    protected function target(): Element
    {
        return $this->template();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function form(): Form
    {
        return $this->form;
    }

    public function title(): Title
    {
        if (!isset($this->title)) {
            $this->title = new Title;
        }
        return $this->title;
    }

    public function setTitle(string|Element|null $content): self
    {
        if (empty($content)) {
            unset($this->title_content);
            $this->title()->setRenderable(false);
        } else {
            $this->title_content = $content;
            if ($this->isRendered()) {
                $this->title()->set($content);
                $this->title()->setRenderable(true);
            }
        }
        return $this;
    }

    public function unsetTitle(): self
    {
        return $this->setTitle(null);
    }

    public function setTitleSize(int $size): self
    {
        $this->title_size = $size;
        if ($this->isRendered()) {
            $this->title()->size($size);
        }
        return $this;
    }

    public function addClass(string|array $classlist): self
    {
        parent::addClass($classlist);
        if ($this->isRendered() && !empty($this->new_classlist)) {
            $this->template()->addClass($this->new_classlist);
        }
        return $this;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;
        if ($this->isRendered()) {
            $this->template()->wrapper()->set($tag);
        }
        return $this;
    }

    // ==================================================================================
    // ---------------------------------- HELPERS ---------------------------------------
    // ----------------------------------------------------------------------------------

    /**
     * Get title size based on parent form title size
     */
    protected function getTitleSize(): int
    {
        return $this->title_size ?? $this->form()->getTitleSize() + 1;
    }

    // ----------------------------------------------------------------------------------
    // ---------------------------------- HELPERS ---------------------------------------
    // ==================================================================================



    // ==================================================================================
    // --------------------------------- STATEMENT --------------------------------------
    // ----------------------------------------------------------------------------------

    protected function isTitleEmpty(): bool
    {
        return empty($this->title_content ?? null);
    }

    // ----------------------------------------------------------------------------------
    // --------------------------------- STATEMENT --------------------------------------
    // ==================================================================================



    // ==================================================================================
    // -------------------------------- RENDER LOGIC ------------------------------------
    // ----------------------------------------------------------------------------------
    
    public function refreshRender(): void
    {
        if (!$this->isRendered()) {
            return;
        }
        $this->template = new Element;
        $this->title = new Title;
        $this->statement_render = false;
    }

    public function render(): Element
    {
        $this->prepareWrapper();
        $this->renderTitle();
        $this->renderFields();
        $this->statement_render = true;
        return $this->template();
    }

    protected function prepareWrapper(): void
    {
        foreach ($this->attributes as $name => $value) {
            $this->template()->setAttr($name, $value);
        }
        $classlist = $this->classlist;
        if ($this->use_default_class) {
            $default_classlist = $this->form()->getChildClass($this->name(), $this->getClassMod());
            $classlist = array_merge($default_classlist, $classlist);
        }
        $this->template()->addClass($classlist);
    }

    protected function renderTitle(): bool
    {
        if ($this->isTitleEmpty()) {
            return false;
        }
        if ($this->use_default_class) {
            $classlist = $this->form()->getChildClass('title', $this->getClassMod());
            $this->title()->addClass($classlist);
        }
        $this->title()->size($this->getTitleSize());
        $this->title()->set($this->title_content);
        $this->template()->addContent($this->title());
        return true;
    }

    protected function renderFields(): bool
    {
        /** @var array<string, int> $fields */
        $fields = $this->form()->getSectionFields($this->name());
        if (empty($fields)) {
            return false;
        }
        foreach ($fields as $fname => $forder) {
            $this->template()->addContent(
                $this->form()->f($fname)->render()
            );
        }
        return true;
    }

    // ----------------------------------------------------------------------------------
    // -------------------------------- RENDER LOGIC ------------------------------------
    // ==================================================================================
}
