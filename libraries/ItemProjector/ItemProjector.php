<?php

namespace BlogLibrary\ItemProjector;

class ItemProjector extends \Blog\Modules\Library\AbstractLibrary
{
    protected const SRC = [
        'js' => [
            'stack' => [
                'js/ItemProjectorList.min.js',
                'js/ItemProjector.min.js',
                'js/script.min.js'
            ],
            'public' => 'js/item-projector.min.js'
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
