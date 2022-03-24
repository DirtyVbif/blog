<?php

namespace Blog\Controller;

use Blog\Components\AjaxModule;
use Blog\Mediators\AjaxResponse;

class AjaxController extends BaseController
{
    protected AjaxResponse $response;
    
    public function prepare(): void
    {
        $this->parseRequest();
        app()->response()->set($this->response());
        return;
    }

    protected function response(): AjaxResponse
    {
        if (!isset($this->response)) {
            $this->response = new AjaxResponse;
        }
        return $this->response;
    }

    protected function parseRequest(): void
    {
        if ($module_name = app()->router()->arg(2)) {
            $module_name = strtolower($module_name);
            if (method_exists(app(), $module_name) && (app()->$module_name() instanceof AjaxModule)) {
                /** @var \Blog\Components\AjaxModule $module */
                $module = app()->{$module_name}();
                /** @var \Blog\Mediators\AjaxResponse $response */
                $response = $module->ajaxRequest();
                $this->response()->set($response);
            } else {
                $this->response()->setCode(404);
            }
        } else {
            $this->response()->setCode(404);
        }
    }

    public function postRequest(): void
    {
        $this->prepare();
        return;
    }
}
