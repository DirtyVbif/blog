<?php

function app(): \Blog\Blog
{
    return \Blog\Blog::instance();
}

function page(): \Blog\Modules\Templates\Page
{
    app()->page();
}
