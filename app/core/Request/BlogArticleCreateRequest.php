<?php

namespace Blog\Request;

class BlogArticleCreateRequest extends BaseRequest
{
    protected const FIELD_NAMES = [
        'title' => 'Article title',
        'alias' => 'Alias path',
        'preview_src' => 'Image preview src link',
        'preview_alt' => 'Image alt text',
        'summary' => 'Summary',
        'body' => 'Body',
        'author' => 'Author',
        'status' => 'Publishing status'
    ];

    protected const ACCESS_LEVEL = 4;

    protected function rules(): array
    {
        return [
            'csrf-token' => [
                'skip' => true,
            ],
            'title' => [
                'type' => 'string',
                'max_length' => 256,
                'required' => true
            ],
            'alias' => [
                'type' => 'string',
                'max_length' => 256,
                'pattern' => '/[\w\-]*/',
                'required' => true
            ],
            'preview_src' => [
                'type' => 'string',
                'max_length' => 256,
                'required' => false
            ],
            'preview_alt' => [
                'type' => 'string',
                'max_length' => 256,
                'required' => false
            ],
            'summary' => [
                'type' => 'plain_text',
                'max_length' => 512,
                'required' => true
            ],
            'body' => [
                'type' => 'html_text',
                'required' => true
            ],
            'author' => [
                'type' => 'string',
                'max_length' => 50,
                'required' => false
            ],
            'status' => [
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
