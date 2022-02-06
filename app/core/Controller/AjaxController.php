<?php

namespace Blog\Controller;

use Blog\Components\AjaxModule;

class AjaxController extends BaseController
{
    protected array $response;

    public function prepare(): void
    {
        $this->prepareResponse();
        if ($this->isStatus400()) {
            app()->controller('error')->prepare();
            return;
        }
        app()->response()->set($this->getResponse());
        return;
    }

    public function getTitle(): string
    {
        return '';
    }

    protected function prepareResponse(): void
    {
        $this->response = [
            'status' => 200,
            'output' => null
        ];
        $this->parseRequest();
    }

    protected function getResponse(): string
    {
        $response = $this->response ?? null;
        return json_encode($response);
    }

    protected function parseRequest(): void
    {
        if ($module_name = app()->router()->arg(2)) {
            $module_name = strtolower($module_name);
            if (method_exists(app(), $module_name) && (app()->$module_name() instanceof AjaxModule)) {
                /** @var \Blog\Components\AjaxModule $module */
                $module = app()->$module_name();
                $this->resposne = $module->ajaxRequest();
            } else {
                $this->response['status'] = 404;
            }
        } else {
            $this->response['status'] = 404;
        }
        return;
    }

    public function postRequest(): void
    {
        $this->prepare();
        return;
    }

    protected function status(): int
    {
        return $this->response['status'];
    }

    protected function isStatus400(): bool
    {
        return preg_match('/4\d\d/', $this->status());
    }
}
