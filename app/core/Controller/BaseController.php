<?php

namespace Blog\Controller;

use Blog\Modules\Template\Page;

abstract class BaseController
{
    public function prepare(): void
    {
        $page = new Page;
        app()->response()->set($page);
    }
}
