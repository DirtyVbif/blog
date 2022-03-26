<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class Image extends TemplateFacade
{
    protected bool $file_is_image = false;
    protected string $mime_type;
    protected string $alt;
    protected float $width;
    protected float $heigth;
    protected float $max_width;
    protected float $max_height;

    /**
     * @param string $source_path path to source file without leading slash `/`
     */
    public function __construct(
        protected string $source_path
    ) {
        $this->src($this->source_path);
    }

    /**
     * Set image source filename
     */
    public function src(?string $source = null): self|string
    {
        if (is_null($source)) {
            return strPrefix($this->source_path, '/');
        }
        $this->source_path = $source;
        ffstr($this->source_path);
        if (file_exists($this->source_path)) {
            $this->mime_type = mime_content_type($this->source_path);
            $this->file_is_image = preg_match('/^image\/.+$/', $this->mime_type);
        }
        $this->setImageSizes();
        return $this;
    }

    protected function setImageSizes(): void
    {
        if (!$this->file_is_image || !file_exists($this->source_path)) {
            return;
        } else if (preg_match('/image\/svg/i', $this->mime_type)) {
            $xmlget = simplexml_load_string(
                file_get_contents($this->source_path)
            );
            $attr = $xmlget->attributes();
            if (isset($attr['viewBox'])) {
                $image_size = preg_split('/\s+/', (string)$attr['viewBox']);
                $this->width = $image_size[2];
                $this->height = $image_size[3];
            } else if (isset($attr['width'], $attr['height'])) {
                $this->width = (float)$attr['width'];
                $this->height = (float)$attr['height'];
            }
        } else {
            $image_size = getimagesize($this->source_path);
            $this->width = $image_size[0];
            $this->height = $image_size[1];
        }
        return;
    }

    public function width(?float $max_width = null): self|float|null
    {
        if (is_null($max_width)) {
            return isset($this->width) ? round($this->width) : null;
        } else if ($max_width > 0) {
            $this->max_width = $max_width;
            $this->max_height = 0;
        }
        return $this;
    }

    public function height(?float $max_height = null): self|float|null
    {
        if (is_null($max_height)) {
            return isset($this->height) ? round($this->height) : null;
        } else if ($max_height > 0) {
            $this->max_height = $max_height;
            $this->max_width = 0;
        }
        return $this;
    }

    public function alt(?string $alt = null): self|string
    {
        if (is_null($alt)) {
            return $this->alt ?? 'image ' . $this->src();
        }
        $this->alt = htmlspecialchars($alt);
        return $this;
    }
    
    public function tpl(): Element
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element('img');
        }
        return $this->tpl;
    }

    public function render()
    {
        if ($this->file_is_image) {
            $this->changeResolution();
            $this->tpl()
                ->setAttr('src', $this->src())
                ->setAttr('alt', $this->alt())
                ->setAttr('width', $this->width())
                ->setAttr('height', $this->height());
        } else {
            $this->tpl()->tag('div');
            $this->tpl()->setContent(
                "Can't render file '" . PUBDIR . $this->source_path . "' as image."
            );
        }
        return parent::render();
    }

    protected function changeResolution(): void
    {
        if ($this->max_width ?? false) {
            $k = $this->width / $this->max_width;
            $this->width /= $k;
            $this->height /= $k;
        } else if ($this->max_height ?? false) {
            $k = $this->height / $this->max_height;
            $this->width /= $k;
            $this->height /= $k;
        }
        return;
    }
}
