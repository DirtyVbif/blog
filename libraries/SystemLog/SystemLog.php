<?php

namespace BlogLibrary\SystemLog;

class SystemLog extends \Blog\Modules\Library\AbstractLibrary
{
    protected const SRC = [
        'css' => [
            'stack' => [
                'css/system-log.min.css'
            ],
            'public' => 'css/system-log.min.css'
        ]
    ];

    public function use(): void
    {
        $this->checkPublicSources();
        app()->page()->useCss(self::SRC['css']['public']);
    }

    protected function getSources(): object
    {
        return (object)self::SRC;
    }
}