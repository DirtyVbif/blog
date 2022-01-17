<?php

namespace Blog\Controller;

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
            'response' => 0
        ];
    }

    protected function getResponse(): string
    {
        return json_encode($this->response['response']);
    }
}
