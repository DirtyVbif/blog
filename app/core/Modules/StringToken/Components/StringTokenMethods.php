<?php

namespace Blog\Modules\StringToken\Components;

trait StringTokenMethods
{
    public function getTokenSite(array $arguments): string
    {
        $argument = array_shift($arguments);
        $output = '';
        switch ($argument) {
            case 'name':
                $output = app()->manifest()->name;
                break;
            case 'tagline':
                $output = app()->manifest()->tagline;
                break;
            default:
                $output = app()->manifest()->short_name;
        }
        return $output;
    }
}