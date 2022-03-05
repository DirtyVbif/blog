<?php

namespace Blog\Request;

class ArticleRequest extends RequestPrototype
{
    protected const ACCESS_LEVEL = 4;

    protected function rules(): array
    {
        return [
            'csrf-token' => [
                'skip' => true,
            ],
            'title' => [
                '#label' => 'Article title',
                'type' => 'string',
                'max_length' => 256,
                'required' => true
            ],
            'alias' => [
                '#label' => 'Alias path',
                'type' => 'string',
                'max_length' => 256,
                'pattern' => '/[\w\-]*/',
                'required' => true
            ],
            'preview_src' => [
                '#label' => 'Image preview src link',
                'type' => 'string',
                'max_length' => 256,
                'required' => false
            ],
            'preview_alt' => [
                '#label' => 'Image alt text',
                'type' => 'string',
                'max_length' => 256,
                'required' => false
            ],
            'summary' => [
                '#label' => 'Summary',
                'type' => 'plain_text',
                'max_length' => 512,
                'required' => true
            ],
            'body' => [
                '#label' => 'Body',
                'type' => 'html_text',
                'required' => true
            ],
            'author' => [
                '#label' => 'Author',
                'type' => 'string',
                'max_length' => 50,
                'required' => false
            ],
            'status' => [
                '#label' => 'Publishing status',
                'type' => 'boolean'
            ],
        ];
    }
}
