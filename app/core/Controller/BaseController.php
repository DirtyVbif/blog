<?php

namespace Blog\Controller;

abstract class BaseController
{
    public function prepare(): void
    {
        app()->builder()->preparePage();
        app()->response()->set(app()->page());
        app()->page()->setAttr('class', 'page');
        app()->page()->getTitle()->setAttr('class', 'page__title');
        return;
    }

    abstract public function getTitle(): string;
}
