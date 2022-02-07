<?php

namespace Blog\Request;

class CommentRequest extends BaseRequest
{
    protected const FIELD_NAMES = [
        'name' => 'Name',
        'email' => 'E-mail',
        'subject' => 'Comment',
        'parent_id' => 'Parent comment id',
        'article_id' => 'Article id'
    ];

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
            ],
            'parent_id' => [
                'type' => 'int',
                'required' => true
            ],
            'article_id' => [
                'type' => 'int',
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
