<?php

namespace Blog\Request;

class FeedbackRequest extends BaseRequest
{
    protected function rules(): array
    {
        return [
            'name' => [
                '#label' => 'Name',
                'type' => 'string',
                'max_length' => 60,
                'required' => true
            ],
            'email' => [
                '#label' => 'E-mail',
                'type' => 'string',
                'pattern' => '/^\w+@\w+\.[a-zA-Z]{2,}$/',
                'required' => true
            ],
            'subject' => [
                '#label' => 'Message',
                'type' => 'plain_text',
                'required' => true
            ]
        ];
    }
}
