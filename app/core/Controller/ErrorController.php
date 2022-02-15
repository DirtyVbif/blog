<?php

namespace Blog\Controller;

use Blog\Modules\Template\Element;

class ErrorController extends BaseController
{
    protected const ERROR_STATUS_TITLES = [
        403 => '#403 Access denied',
        404 => '#404 Page not found'
    ];

    public function prepare(int $status = 404): void
    {
        $title = t(self::ERROR_STATUS_TITLES[$status] ?? self::ERROR_STATUS_TITLES[404]);
        parent::prepare();
        $this->getTitle()->set($title);
        $content = new Element;
        $content->setName("content/error--{$status}");
        app()->page()->addContent($content);
        app()->page()->useCss('/css/error.min');
        http_response_code($status);
        app()->page()->setMetaTitle(str_replace('#', t('Error') . ' ', $title) . ' | mublog.site');
        return;
    }
    
    public function getTitle(): ControlledPageTitle
    {
        parent::getTitle();
        if (!$this->title->isset()) {
            $this->title->set(t(self::ERROR_STATUS_TITLES[404]));
        }
        return $this->title;
    }

    public function postRequest(): void
    {
        pre($_POST);
        exit;
    }
}
