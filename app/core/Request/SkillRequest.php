<?php

namespace Blog\Request;

class SkillRequest extends RequestPrototype
{
    protected const ACCESS_LEVEL = 4;
    protected const CSRF_SKIP = true;

    public function rules(): array
    {
        return [
            'title' => [
                '#label' => 'Skill label',
                'validator:type' => 'string',
                'validator:strlenmax' => 256,
                'validator:required' => true
            ],
            'icon_src' => [
                '#label' => 'Skill icon src link',
                'validator:type' => 'string',
                'validator:strlenmax' => 256,
                'validator:required' => true
            ],
            'icon_alt' => [
                '#label' => 'Skill icon alt text',
                'validator:type' => 'string',
                'validator:strlenmax' => 256,
                'validator:required' => true
            ],
            'body' => [
                '#label' => 'Skill body',
                'validator:type' => 'string',
                'validator:required' => true,
                'formatter:html' => 'full'
            ],
            'status' => [
                '#label' => 'Publishing status',
                'preprocessor:default-value' => 0,
                'validator:type' => 'int'
            ]
        ];
    }
}
