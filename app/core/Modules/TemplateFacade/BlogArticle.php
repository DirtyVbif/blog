<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class BlogArticle extends TemplateFacade
{
    public function __construct(
        protected array $data,
        protected string $view_mode = 'full'
    ) {
        
    }

    /** @return Element */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element('article');
        }
        return $this->tpl;
    }

    public function render()
    {
        $this->tpl()->setName('content/article--' . $this->view_mode);
        foreach ($this->data as $key => $value) {
            $this->tpl()->set($key, $value);
        }
        return parent::render();
    }
}
