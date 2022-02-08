<?php

namespace Blog\Controller;

class CustomController extends BaseController
{
    protected int $status;
    protected string $specified_request_method;

    public function prepare(): void
    {
        parent::prepare();
        $this->status = 200;
        if (!$this->validateRequest()) {
            // if blog arguments is invalide then load error controller with status 404
            /** @var ErrorController $err_c */
            $err_c = app()->controller('error');
            $err_c->prepare($this->status);
            return;
        }
        $this->{$this->specified_request_method}();
        // // add main page elements
        // app()->page()->setAttr('class', 'page_front');
        // // use front page styles
        // app()->page()->useCss('front.min');
        // // add page content
        // app()->page()->addContent([
        //     // set front page banner
        //     app()->builder()->getBannerBlock(),
        //     // set front page skill box
        //     app()->builder()->getSkillsBlock(),
        //     // set front page summary block
        //     app()->builder()->getSummaryBlock(),
        //     // set front page blog preview block
        //     app()->builder()->getBlogPreview(),
        //     // set front page contacts block
        //     app()->builder()->getContactsBlock()
        // ]);
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

    public function getTitle(): string
    {
        return '';
    }

    public function postRequest(): void
    {
        pre($_POST);
        exit;
    }

    protected function getRequestAgreementCookie(): void
    {
        app()->page()->setTitle('Политика использования cookie-файлов');
    }
}
