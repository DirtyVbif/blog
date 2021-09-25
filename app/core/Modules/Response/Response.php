<?php

namespace Blog\Modules\Response;

class Response
{
    protected $output;

    public function render()
    {
        return $this->output ?? 'Error 404';
    }

    public function set($output): void
    {
        $this->output = $output;
        return;
    }
}
