<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class Link extends TemplateFacade
{
    protected string $url;
    protected string $label;

    public function __construct(
        protected string $name
    ) {
       $data = app()->builder()->getLink($this->name);
       $this->url = $data['url'];
       $this->label = $data['label'];
    }

    public function __get(string $name)
    {
        $synonyms = [
            'href' => 'url'
        ];
        $name = $synonyms[$name] ?? $name;
        return $this->$name;
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
        $this->setAttr('href', $this->url);
        return parent::render();
    }
}
