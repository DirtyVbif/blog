<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;
use Twig\Markup;

class BlogArticle extends TemplateFacade
{
    public const VIEW_MODE_FULL = 'full';
    public const VIEW_MODE_TEASER = 'teaser';
    public const VIEW_MODE_PREVIEW = 'preview';
    protected const VIEW_MODES = [
        0 => self::VIEW_MODE_FULL,
        1 => self::VIEW_MODE_TEASER,
        2 => self::VIEW_MODE_PREVIEW
    ];

    protected string $view_mode;

    public function __construct(
        protected array $data,
        $view_mode = self::VIEW_MODE_FULL
    ) {
        $this->setViewMode($view_mode);
    }

    public function __get(string $name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
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
            if ($key === 'body') {
                $value = new Markup($value, CHARSET);
            }
            $this->tpl()->set($key, $value);
        }
        return parent::render();
    }

    /**
     * @param string $view_mode is name of view mode. Also named constants are available:
     * * BlogArticle::VIEW_MODE_FULL
     * * BlogArticle::VIEW_MODE_TEASER
     * * BlogArticle::VIEW_MODE_PREVIEW
     */
    public function setViewMode(string $view_mode): self
    {
        if (in_array($view_mode, self::VIEW_MODES)) {
            $this->view_mode = $view_mode;
        } else {
            $this->view_mode = self::VIEW_MODE_FULL;
        }
        return $this;
    }
}
