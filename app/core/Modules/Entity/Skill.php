<?php

namespace Blog\Modules\Entity;

use Blog\Modules\Template\Element;
use Blog\Request\RequestPrototype;

class Skill extends EntityPrototype
{
    public const ENTITY_DATA_TABLE = 'entities_skill_data';
    public const ENTITY_DATA_COLUMNS = ['title', 'body', 'icon_src', 'icon_alt'];
    /** @var int entity type id (etid) specified in entities_types table */
    public const ENTITY_TYPE_ID = 3;        // skill
    public const VIEW_MODE_FULL = 'full';
    public const URL_MASK = '/admin/skill/%d';

    public static function getSqlTableName(): array|string
    {
        return ['e_s' => self::ENTITY_DATA_TABLE];
    }

    public static function getSqlTableColumns(): array
    {
        return ['e_s' => self::ENTITY_DATA_COLUMNS];
    }

    public static function count(): int
    {
        return sql_select(from: self::ENTITY_DATA_TABLE)->count();
    }

    /**
     * @param array $options recieves SQL QUERY SELECT options:
     * * array key @var int 'limit'
     * * array key @var int 'offset'
     * * array key @var string 'order' => ASC or DESC
     * * array key @var string 'view_mode'
     * @return Skill[]
     */
    public static function loadList(array $options): array
    {
        $skills = [];
        $sql = self::sql();
        $sql->where(['et.etid' => self::ENTITY_TYPE_ID]);
        $sql->order('e.created', $options['order'] ?? 'ASC');
        $sql->limit($options['limit'] ?? null);
        $sql->limitOffset($options['offset'] ?? null);
        $view_mode = $options['view_mode'] ?? self::VIEW_MODE_FULL;
        foreach ($sql->all() as $skill) {
            $skills[$skill['id']] = new self($skill, $view_mode);
        }
        return $skills;
    }
    
    /**
     * @param \Blog\Request\SkillRequest $request
     */
    public static function create(RequestPrototype $request, ?array $data = null): bool
    {
        sql()->startTransation();
        $sql = sql_insert('entities');
        $sql->set([self::ENTITY_TYPE_ID], ['etid']);
        $rollback = true;
        if ($entity_id = $sql->exe()) {
            $sql = sql_insert(self::ENTITY_DATA_TABLE);
            $sql->set(
                [$entity_id, $request->title, $request->body, $request->icon_src, $request->icon_alt],
                ['eid', 'title', 'body', 'icon_src', 'icon_alt']
            );
            $result = $sql->exe(true);
            if ($result) {
                $rollback = false;
                $request->complete();
            }
        }
        sql()->commit($rollback);
        return !$rollback;
    }

    /**
     * @return Element $tpl
     */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element;
        }
        return $this->tpl;
    }

    public function render()
    {
        $this->tpl()->setName('content/skill--' . $this->view_mode);
        $this->tpl()->setId('skill-' . $this->id());
        foreach ($this->data as $key => $value) {
            $this->tpl()->set($key, $value);
        }
        return parent::render();
    }

    public function url(): ?string
    {
        if ($this->id()) {
            return sprintf(self::URL_MASK, $this->id());
        }
        return null;
    }
}
