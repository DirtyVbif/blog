<?php

namespace Blog\Modules\Template;

class PageFooter extends BaseTemplate
{
    protected string $template_name = 'page--footer';

    public function __construct()
    {
        parent::__construct($this->template_name);
    }
}
