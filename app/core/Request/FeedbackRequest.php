<?php

namespace Blog\Request;

class FeedbackRequest extends RequestPrototype
{
    public function rules(): array
    {
        return [
            'name' => [
                '#label' => 'Name',
                'validator:type' => 'string',
                'validator:strlenmax' => 60,
                'validator:required' => true
            ],
            'email' => [
                '#label' => 'E-mail',
                'validator:email',
                'validator:required' => true
            ],
            'subject' => [
                '#label' => 'Message',
                'validator:type' => 'string',
                'validator:required' => true,
                'formatter:html-text' => 'basic'
            ]
        ];
    }

    public function label(): string
    {
        return 'Feedback from ' . $this->raw('email');
    }
}
