<?php

namespace Blog\Controller;

class ErrorController extends BaseController
{
    public function getTitle(): string
    {
        return 'Error 404. Page not found.';
    }
}
