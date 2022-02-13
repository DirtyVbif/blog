<?php

namespace Blog\Components;

interface AjaxModule
{
    /**
     * @return array must return at least keys:
     * 'output' => 'ajax response output'
     * 'status' => 'response status code'
     */
    public function ajaxRequest(): array;
}