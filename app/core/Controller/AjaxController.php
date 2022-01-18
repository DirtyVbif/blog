<?php

namespace Blog\Controller;

use Blog\Components\AjaxModule;

class AjaxController extends BaseController
{
    protected array $response;

    public function prepare(): void
    {
        $this->prepareResponse();
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
            'output' => 1
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
                return;
            }
        }
    }
}
