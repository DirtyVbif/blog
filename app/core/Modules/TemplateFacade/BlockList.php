<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class BlockList extends TemplateFacade
{
    protected bool $unnamed = true;
    protected array $classlist = [];

    public function __construct(
        protected array $items
    ) {

    }
    
    public function tpl(): Element
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element($this->unnamed ? 'ul' : 'ol');
            $this->tpl->setName('blocks/list');
        }
        return $this->tpl;
    }

    public function render()
    {
        if (empty($this->items)) {
            $this->renderable = false;
        } else if (!empty($this->classlist)) {
            $this->tpl()->addClass(implode(' ', $this->classlist));
            $this->set(
                'items_class',
                classlistToString($this->classlist, '', '__item')
            );
        }
        $this->set('items', $this->items);
        return parent::render();
    }

    public function set(string $name, $value): self
    {
        $this->tpl()->set($name, $value);
        return $this;
    }

    public function setClasslist(string|array $classlist): self
    {
        if (is_string($classlist)) {
            $classlist = preg_split('/[\s\.\/\\\]+/', $classlist);
        }
        foreach ($classlist as $class) {
            $this->classlist[$class] = $class;
        }
        return $this;
    }

    /**
     * Set current list as unnamed list `<ul>` or ordered list `<ol>`
     */
    public function unnamed(bool $unnamed = true): self
    {
        $this->unnamed = $unnamed;
        return $this;
    }
}