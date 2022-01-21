<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class BodyText extends TemplateFacade
{
    protected array $content = [];

    public function __construct(
        protected string $raw_text
    ) {
        $this->setContent($this->raw_text);
    }

    /**
     * @return Element
     */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element;
        }
        return $this->tpl;
    }

    public function setContent(string $raw_text): self
    {
        $content = preg_split('/\n+/', $raw_text);
        foreach ($content as $i => &$part) {
            $part = preg_replace('/(^\s+|\s+$)/', '', $part);
            if (strlen($part) < 1) {
                unset($content[$i]);
                continue;
            }
            $paragraph = new Element('p');
            $paragraph->setContent($part);
            $part = $paragraph;
        }
        if (!empty($content)) {
            $this->content = $content;
        }
        return $this;
    }

    public function render()
    {
        if (empty($this->content ?? [])) {
            return '';
        }
        $this->tpl()->wrapper()->hide();
        $this->tpl()->setContent(
            implode($this->content)
        );
        return parent::render();
    }
}
