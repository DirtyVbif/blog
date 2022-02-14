<?php

namespace BlogLibrary;

class AdminBar extends \Blog\Modules\Library\AbstractLibrary
{
    protected const SRC = [
        'js' => [
            'stack' => [
                'js/script.min.js'
            ],
            'public' => 'js/admin-bar.min.js'
        ]
    ];

    public function use(): void
    {
        $this->checkPublicSources();
        app()->page()->useJs(self::SRC['js']['public']);
    }

    protected function getSources(): object
    {
        return (object)self::SRC;
    }
}