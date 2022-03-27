<?php

namespace Blog\Interface\Form;

use Blog\Interface\TemplateInterface;
use Blog\Modules\Template\Element;
use Blog\Modules\TemplateFacade\Title;

class FormSection implements FormSectionInterface, TemplateInterface
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
     * @param string $name must be exactly in `kebab-case-style`. Any other styles will be converted to that case
     * @param Form $form parent form object
     */
    public function __construct(
        string $name,
        protected Form $form
    ) {
        $this->name = kebabCase($name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function form(): Form
    {
        return $this->form;
    }

    public function template(): Element
    {
        if (!isset($this->template)) {
            $this->template = new Element;
        }
        return $this->template;
    }

    public function title(): Title
    {
        if (!isset($this->title)) {
            $this->title = new Title();
        }
        return $this->title;
    }

    public function setTitle(string|Element|null $content): self
    {
        if (empty($content)) {
            unset($this->title_content);
        } else {
            $this->title_content = $content;
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
        return $this;
    }

    public function addClass(string|array $classlist): self
    {
        if (is_string($classlist)) {
            $classlist = preg_split('/\s+/', $classlist);
        }
        foreach ($classlist as $class) {
            $class = normalizeClassname($class);
            if (!in_array($class, $this->classlist)) {
                array_push($this->classlist, $class);
            }
        }
        return $this;
    }

    public function setClassMod(string $mod): self
    {
        $this->classlist_mod = $mod;
        return $this;
    }

    public function useDefaultClass(bool $use): self
    {
        $this->use_default_class = $use;
        return $this;
    }

    public function setAttribute(string $name, ?string $value = null, bool $data_attribute = false): self
    {
        if ($data_attribute) {
            // remove manualy provided data-prefix
            $name = preg_replace('/^\W*data\W+/', '', $name);
        }
        $name = kebabCase($data_attribute ? "data {$name}" : $name);
        $this->attributes[$name] = $value;
        return $this;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    // ==================================================================================
    // ---------------------------------- HELPERS ---------------------------------------
    // ----------------------------------------------------------------------------------

    /**
     * Get title size based on parent form title size
     */
    protected function tSize(): int
    {
        return $this->title_size ?? $this->form()->getTitleSize() + 1;
    }

    protected function getClasslistMod(): ?string
    {
        return $this->classlist_mod ?? $this->name();
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

    public function render(): Element
    {
        // TODO: complete changes affeting on rendered section element
        $this->prepareWrapper();
        $this->renderTitle();
        $this->renderFields();
        return $this->template();
    }

    protected function prepareWrapper(): void
    {
        foreach ($this->attributes as $name => $value) {
            $this->template()->setAttr($name, $value);
        }
        $classlist = $this->classlist;
        if ($this->use_default_class) {
            $default_classlist = $this->form()->getItemClasslist($this->name(), $this->getClasslistMod());
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
            $classlist = $this->form()->getItemClasslist('title', $this->getClasslistMod());
            $this->title()->addClass($classlist);
        }
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
