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
        $this->setDefaultMeta();
        return;
    }

    abstract public function getTitle(): string;

    abstract public function postRequest(): void;

    protected function setDefaultMeta(): void
    {
        app()->page()->setMeta('keywords', [
            'name' => 'keywords',
            'content' => 'веб-разработка, блог, резюме, портфолио, php-разработчик, front-end, back-end, full-stack'
        ]);
        app()->page()->setMeta('icon', [
            'rel' => 'icon',
            'href' => fullUrlTo('/favicon.svg'),
            'type' => 'image/svg+xml'
        ], 'link');
        app()->page()->setMeta('shortcut icon', [
            'rel' => 'shortcut icon',
            'href' => fullUrlTo('/favicon.svg')
        ], 'link');
        app()->page()->setMeta('apple-touch-icon', [
            'rel' => 'apple-touch-icon',
            'href' => fullUrlTo('/favicon.svg')
        ], 'link');
        app()->page()->setMeta('image_src', [
            'rel' => 'image_src',
            'href' => fullUrlTo('/favicon.svg')
        ], 'link');
        app()->page()->setMeta('og:image', [
            'protperty' => 'og:image',
            'content' => fullUrlTo('/logo.svg')
        ]);
        app()->page()->setMeta('canonical', [
            'rel' => 'canonical',
            'href' => fullUrlTo(app()->router()->url())
        ], 'link');
        return;
    }
}
