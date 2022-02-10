<?php

namespace Blog\Request;

class LoginRequest extends BaseRequest
{
    protected const FIELD_NAMES = [
        'mail' => 'Login',
        'password' => 'Password',
        'remember_me' => 'Remember login session'
    ];
    protected const ACCESS_LEVEL = 1;

    protected function rules(): array
    {
        return [
            'mail' => [
                'type' => 'string',
                'pattern' => '/^\w+@\w+\.[a-zA-Z]{2,}$/',
                'required' => true
            ],
            'password' => [
                'type' => 'string',
                'pattern' => "/[\w\@\%\#\!\?\&\$\-]{8,40}/",
                'required' => true
            ],
            'remember_me' => [
                'type' => 'boolean'
            ],
        ];
    }

    public function __get($name)
    {
        if (isset(self::FIELD_NAMES[$name]) && $this->isValid()) {
            return $this->data[$name];
        }
    }

    protected function getFieldName(string $name): string
    {
        return t(self::FIELD_NAMES[$name] ?? $name);
    }
}
