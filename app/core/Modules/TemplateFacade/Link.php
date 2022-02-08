<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class Link extends TemplateFacade
{
    protected string $name;
    protected string $url;
    protected string $label;
    protected string $hash_base_path;

    public function __construct(
        string $link,
        ?string $hash_base_path = null
    ) {
        $data = app()->builder()->getLink($link);
        if ($data) {
            $this->name = $link;
            $this->url = $data['url'];
            $this->label = $data['label'];
            $hash_base_path ??= $data['hash_base'] ?? null;
        } else {
            $this->url = $link;
        }
        if ($hash_base_path) {
            $this->setHashBasePath($hash_base_path);
        }
    }

    public function __get(string $name)
    {
        if ($name === 'url') {
            return $this->raw();
        } else if (isset($this->$name)) {
            return $this->$name;
        }
    }

    public function label(?string $label = null): self|string|null
    {
        if (is_null($label)) {
            return $this->label ?? null;
        }
        $this->label = $label;
        return $this;
    }

    /**
     * @return Element $tpl
     */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element('a');
            $this->tpl->safety(true);
        }
        return $this->tpl;
    }

    public function render(?string $label = null)
    {
        $this->tpl()->setContent(($label ?? $this->label));
        $this->setAttr('href', $this->raw());
        return parent::render();
    }

    public function raw(): string
    {
        $prefix = '';
        if (isset($this->hash_base_path) && $this->hash_base_path !== app()->router()->getCurrentUrl()) {
            $prefix = $this->hash_base_path;
        }
        return $prefix . $this->url;
    }

    public function setHashBasePath(string $base_path): self
    {
        $this->hash_base_path = $base_path;
        return $this;
    }
}
