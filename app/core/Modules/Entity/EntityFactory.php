<?php

namespace Blog\Modules\Entity;

use JetBrains\PhpStorm\ExpectedValues;

class EntityFactory
{
    public static function getNewId(): int
    {
        $schema = app()->env()->DB['SCHEMA'] ?? app()->env()->DB['NAME'];
        $sql = sql()->query(
            'SELECT AUTO_INCREMENT'
            . ' FROM information_schema.TABLES'
            . ' WHERE TABLE_SCHEMA = "' . $schema . '"'
            . ' AND `TABLE_NAME` = "entities";');
        $result = $sql->fetch();
        return (int)$result['AUTO_INCREMENT'];
    }

    public static function getTypeById(int $entity_id): ?string
    {
        $sql = sql_select(from: ['e' => 'entities']);
        $sql->join(table: ['et' => 'entities_types'], using: 'etid');
        $sql->columns(['et' => ['name']]);
        $sql->where(['eid' => $entity_id]);
        $result = $sql->first();
        return $result['name'] ?? null;
    }
    
    public static function load(int $entity_id, ?string $entity_type = null): ?EntityPrototype
    {
        $entity_type ??= self::getTypeById($entity_id);
        switch ($entity_type) {
            case 'article':
                return new Article($entity_id);
            case 'feedback':
                return new Feedback($entity_id);
            default:
                return null;
        }
    }

    /**
     * Load list of entities
     * 
     * @return Comment[]|Article[]|Feedback[]
     */
    public static function loadList(
        #[ExpectedValues('comment', 'feedback', 'article')]
        string $entity_type,
        array $options = []
    ): array {
        switch ($entity_type) {
            case 'comment':
                return Comment::loadList($options);
            case 'feedback':
                return Feedback::loadList($options);
            case 'article':
                return Article::loadList($options);
            default:
                // TODO: set throwable error
                pre([
                    'error' => "Unknown entity type {$entity_type}. Entity list of specified type cannot be loaded."
                ]);
                exit;
        }
    }
}
