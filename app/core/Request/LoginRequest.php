<?php

namespace Blog\Request;

class LoginRequest extends BaseRequest
{
    protected const ACCESS_LEVEL = 1;

    protected function rules(): array
    {
        return [
            'mail' => [
                '#label' => 'Login',
                'type' => 'string',
                'pattern' => '/^\w+@\w+\.[a-zA-Z]{2,}$/',
                'required' => true
            ],
            'password' => [
                '#label' => 'Password',
                'type' => 'string',
                'pattern' => "/[\w\@\%\#\!\?\&\$\-]{8,40}/",
                'required' => true
            ],
            'remember_me' => [
                '#label' => 'Remember login session',
                'type' => 'boolean'
            ],
        ];
    }
}
