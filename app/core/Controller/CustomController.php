<?php

namespace Blog\Controller;

use Blog\Modules\Template\Element;
use Blog\Modules\TemplateFacade\BlockList;
use Blog\Modules\User\User;

class CustomController extends BaseController
{
    protected int $status;
    protected string $specified_request_method;

    public function prepare(): void
    {
        parent::prepare();
        $this->status = 200;
        if (
            !$this->validateRequest()
            || !$this->{$this->specified_request_method}()
        ) {
            // if blog arguments is invalide then load error controller with status 404
            /** @var ErrorController $conerr */
            $conerr = app()->controller('error');
            $conerr->prepare($this->status);
            return;
        }
        return;
    }

    protected function validateRequest(): bool
    {
        if ($sub_argument = app()->router()->arg(2)) {
            // there is no custom pages for now for request of 2nd level
            $this->status = 404;
            return false;
        }
        // check get request
        $argument = app()->router()->arg(1);
        $method = pascalCase("get request {$argument}");
        if (method_exists($this, $method)) {
            // if request is correct use specified method
            $this->specified_request_method = $method;
            return true;
        }
        $this->status = 404;
        return false;
    }

    public function postRequest(): void
    {
        pre($_POST);
        exit;
    }

    protected function setTitles(string $title): void
    {
        $this->getTitle()->set($title);
        app()->page()->setMetaTitle($title . stok(' | :[site]'));
        return;
    }

    protected function getRequestAgreementCookie(): bool
    {
        $this->setTitles('Политика использования cookie-файлов');
        app()->page()->setMeta(
            'name',
            [
                'name' => 'description',
                'content' => 'Основные положения и политика исопльзования cookie-файлов'
        ]);
        $cookie_agreement = new Element;
        $cookie_agreement->setName('content/cookie-agreement');
        $cookie_agreement->addClass('cookie-files');
        app()->page()->addContent([$cookie_agreement]);
        return true;
    }

    protected function getRequestFeedbacks(): bool
    {
        if (!app()->user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN)) {
            $this->status = 403;
            return false;
        }
        $this->setTitles('User\'s feedbacks');
        // set meta tag robots as noindex
        // reason is that feedbacks page available only for admins
        app()->page()->metaRobots('noindex');
        app()->page()->useCss('/css/feedbacks.min');
        $list = new BlockList(app()->view('feedbacks')->view()->items);
        $list->set(
            'pager',
            app()->view('feedbacks')->view()->pager
        )->setClasslist('feedbacks');
        app()->page()->addContent($list);
        return true;
    }
}
