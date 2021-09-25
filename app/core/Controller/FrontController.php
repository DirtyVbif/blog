<?php

namespace Blog\Controller;

class FrontController extends BaseController
{
    public function getTitle(): string
    {
        return 'Blog page';
    }
}
