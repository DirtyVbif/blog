<?php

namespace Blog\Request;

class CommentRequest extends RequestPrototype
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
                '#label' => 'Comment',
                'validator:type' => 'string',
                'validator:required' => true,
                'formatter:html-text' => 'basic'
            ],
            'parent_id' => [
                '#label' => 'Parent comment id',
                'validator:type' => 'int',
            ],
            'entity_id' => [
                '#label' => 'Entity id',
                'validator:type' => 'int',
                'validator:required' => true
            ]
        ];
    }
}
