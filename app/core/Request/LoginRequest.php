<?php

namespace Blog\Request;

class LoginRequest extends RequestPrototype
{
    protected const ACCESS_LEVEL = 1;

    public function rules(): array
    {
        return [
            'mail' => [
                '#label' => 'Login',
                'validator:email',
                'validator:required' => true
            ],
            'password' => [
                '#label' => 'Password',
                'validator:type' => 'string',
                'validator:strlenmin' => 8,
                'validator:strlenmax' => 64,
                'validator:pattern' => "/[\w\@\%\#\!\?\&\$\-]+/",
                'validator:required' => true
            ],
            'remember_me' => [
                '#label' => 'Remember login session',
                'validator:type' => 'bool',
            ]
        ];
    }

    public function label(): string
    {
        return 'Login as ' . $this->raw('mail');
    }
}
