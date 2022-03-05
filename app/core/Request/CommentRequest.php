<?php

namespace Blog\Request;

class CommentRequest extends RequestPrototype
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
                '#label' => 'Comment',
                'type' => 'plain_text',
                'required' => true
            ],
            'parent_id' => [
                '#label' => 'Parent comment id',
                'type' => 'int',
                'required' => true
            ],
            'entity_id' => [
                '#label' => 'Entity id',
                'type' => 'int',
                'required' => true
            ]
        ];
    }
}
