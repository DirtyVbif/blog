<?php

namespace Blog\Request;

class FeedbackRequest extends BaseRequest
{
    protected const FIELD_NAMES = [
        'name' => 'Name',
        'email' => 'E-mail',
        'subject' => 'Message'
    ];
    protected const ACCESS_LEVEL = 2;

    protected function rules(): array
    {
        return [
            'name' => [
                'type' => 'string',
                'max_length' => 60,
                'required' => true
            ],
            'email' => [
                'type' => 'string',
                'pattern' => '/^\w+@\w+\.[a-zA-Z]{2,}$/',
                'required' => true
            ],
            'subject' => [
                'type' => 'plain_text',
                'required' => true
            ]
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
