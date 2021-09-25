<?php

namespace Blog\Modules\Template;

class PageHeader extends BaseTemplate
{
    protected string $template_name = 'page--header';

    public function __construct()
    {
        parent::__construct($this->template_name);
    }
}
