<?php

namespace Blog\Request;

use Blog\Modules\Entity\Article;

class ArticleRequest extends RequestPrototype
{
    protected const ACCESS_LEVEL = 4;
    protected const CSRF_SKIP = true;

    public function rules(): array
    {
        return [
            'title' => [
                '#label' => 'Article label',
                'validator:type' => 'string',
                'validator:strlenmax' => 256,
                'validator:required' => true
            ],
            'alias' => [
                '#label' => 'Aliased path',
                'preprocessor:article-alias' => 'title',
                'validator:type' => 'string',
                'validator:strlenmax' => 256,
                'validator:pattern' => '/[\w\-]*/'
            ],
            'preview_src' => [
                '#label' => 'Image preview src link',
                'preprocessor:default-value' => Article::DEFAULT_PREVIEW_SRC,
                'validator:type' => 'string',
                'validator:strlenmax' => 256
            ],
            'preview_alt' => [
                '#label' => 'Image alt text',
                'preprocessor:default-value' => Article::DEFAULT_PREVIEW_ALT,
                'validator:type' => 'string',
                'validator:strlenmax' => 256
            ],
            'summary' => [
                '#label' => 'Summary',
                'validator:type' => 'string',
                'validator:strlenmax' => 512,
                'validator:required' => true,
                'formatter:plain-text'
            ],
            'body' => [
                '#label' => 'Article body',
                'validator:type' => 'string',
                'validator:required' => true,
                'formatter:html-text' => 'full'
            ],
            'author' => [
                '#label' => 'Article author',
                'preprocessor:default-value' => Article::DEFAULT_AUTHOR,
                'validator:type' => 'string',
                'validator:strlenmax' => 48
            ],
            'status' => [
                '#label' => 'Publishing status',
                'preprocessor:default-value' => 0,
                'validator:type' => 'int'
            ],
            'views' => [
                '#label' => 'Views count',
                'preprocessor:default-value' => 1,
                'validator:type' => 'int'
            ],
            'rating' => [
                '#label' => 'Article rating',
                'preprocessor:default-value' => 1,
                'validator:type' => 'int'
            ]
        ];
    }

    public function label(): string
    {
        return $this->raw('title');
    }
}
