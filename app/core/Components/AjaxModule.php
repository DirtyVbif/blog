<?php

namespace Blog\Components;

use Blog\Mediators\AjaxResponse;

interface AjaxModule
{
    /**
     * @return array must return at least keys:
     * 'output' => 'ajax response output'
     * 'status' => 'response status code'
     */
    public function ajaxRequest(): AjaxResponse;
}