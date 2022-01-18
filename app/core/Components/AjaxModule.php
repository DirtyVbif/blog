<?php

namespace Blog\Components;

interface AjaxModule
{
    public function ajaxRequest(): array;
}