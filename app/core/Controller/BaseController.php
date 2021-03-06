<?php

namespace Blog\Controller;

abstract class BaseController
{
    protected ControlledPageTitle $title;

    abstract public function postRequest(): void;

    public function prepare(): void
    {
        if (!app()->builder()->prepared()) {
            app()->builder()->preparePage();
            app()->response()->set(app()->page());
            app()->page()->setAttr('class', 'page');
            app()->page()->getTitle()->setAttr('class', 'page__title');
            $this->setDefaultMeta();
        }
        return;
    }

    protected function setDefaultMeta(): void
    {
        app()->page()->setMeta('favicon', [
            'rel' => 'shortcut icon',
            'href' => fullUrlTo('/favicon.ico'),
            'sizes' => 'any'
        ], 'link');
        app()->page()->setMeta('favicon-svg', [
            'rel' => 'icon',
            'href' => fullUrlTo('/favicon.svg'),
            'type' => 'image/svg+xml'
        ], 'link');
        app()->page()->setMeta('apple-touch-icon', [
            'rel' => 'apple-touch-icon',
            'href' => fullUrlTo('/favicon.png')
        ], 'link');
        app()->page()->setMeta('manifest', [
            'rel' => 'manifest',
            'href' => fullUrlTo('/manifest.json')
        ], 'link');
        app()->page()->setMeta('keywords', [
            'name' => 'keywords',
            'content' => app()->manifest()->keywords
        ]);
        app()->page()->setMeta('canonical', [
            'rel' => 'canonical',
            'href' => fullUrlTo(app()->router()->url())
        ], 'link');
        app()->page()->setMeta('og:image', [
            'property' => 'og:image',
            'content' => fullUrlTo('/logo.svg')
        ]);
        return;
    }
    
    public function getTitle(): ControlledPageTitle
    {
        if (!isset($this->title)) {
            $this->title = new ControlledPageTitle;
        }
        return $this->title;
    }
}
