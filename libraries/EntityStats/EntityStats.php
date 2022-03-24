<?php

namespace BlogLibrary\EntityStats;

use Blog\Client\User;
use Blog\Modules\Template\Element;

class EntityStats extends \Blog\Modules\Library\AbstractLibrary
{
    protected const SRC = [
        'js' => [
            'stack' => [
                'js/EntityStatsRating.min.js',
                'js/EntityStatsViews.min.js',
                'js/EntityStatsElement.min.js',
                'js/EntityController.min.js',
                'js/script.min.js'
            ],
            'public' => 'js/entity-stats.min.js'
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

    public function prepareTemplate(
        Element $tpl,
        int $entity_id,
        string $entity_type
    ): void {
        $tpl->addClass('js-entity');
        $tpl->setAttr('data-entity-id', $entity_id);
        $tpl->setAttr('data-entity-type', $entity_type);
        if (user()->hasMasterIp() || user()->verifyAccessLevel(User::ACCESS_LEVEL_ADMIN)) {
            $tpl->setAttr('data-entity-disable', 'views');
        }
    }
}
