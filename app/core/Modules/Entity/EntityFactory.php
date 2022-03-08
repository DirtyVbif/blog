<?php

namespace Blog\Modules\Entity;

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
    
    public static function load(int $entity_id): ?EntityPrototype
    {
        switch (self::getTypeById($entity_id)) {
            case 'article':
                return new Article($entity_id);
            case 'feedback':
                return new Feedback($entity_id);
            default:
                return null;
        }
    }
}
