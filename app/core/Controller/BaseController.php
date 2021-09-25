<?php

namespace Blog\Controller;

use Blog\Modules\Template\Page;

abstract class BaseController
{
    public function prepare(): void
    {
        app()->builder()->preparePage();
        app()->response()->set(app()->page());
        return;
    }

    abstract public function getTitle(): string;
}
