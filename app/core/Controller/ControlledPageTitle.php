<?php

namespace Blog\Controller;

class ControlledPageTitle
{
    protected string $content = '';

    public function __toString()
    {
        return $this->content;
    }

    public function set(string $content): void
    {
        $this->content = $content;
        return;
    }

    public function isset(): bool
    {
        return (bool)$this->content;
    }
}
