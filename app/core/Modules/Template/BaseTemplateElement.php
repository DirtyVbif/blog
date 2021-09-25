<?php

namespace Blog\Modules\Template;

use Twig\Markup;

abstract class BaseTemplateElement
{
    public function __toString()
    {
        return (string)$this->render();
    }

    abstract public function render();

    /**
     * @param string|string[] $data
     * @return \Twig\Markup|\Twig\Markup[]|null
     */
    protected function markup(array|string $data)
    {
        if (is_array($data)) {
            foreach ($data as $i => $value) {
                $data[$i] = $this->markup($value);
            }
            return $data;
        } else if (is_string($data)) {
            return new Markup($data, CHARSET);
        }
        return null;
    }
}
