<?php

namespace Blog\Modules\Entity;

use Blog\Client\User;
use Blog\Modules\Template\Element;
use Blog\Request\RequestPrototype;
use JetBrains\PhpStorm\ExpectedValues;
use Twig\Markup;

class Skill extends EntityPrototype
{
    public const ENTITY_DATA_TABLE = 'entities_skill_data';
    public const ENTITY_DATA_COLUMNS = ['title', 'body', 'icon_src', 'icon_alt'];
    /** @var int entity type id (etid) specified in entities_types table */
    public const ENTITY_TYPE_ID = 3;        // skill
    public const VIEW_MODE_FULL = 'full';
    public const VIEW_MODE_TEASER = 'teaser';
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
            $rollback = $result ? false : true;
        }
        sql()->commit($rollback);
        return !$rollback;
    }

    /**
     * @param \Blog\Request\SkillRequest $request
     */
    public static function edit(int $id, RequestPrototype $request): bool
    {
        
        $sql = sql_update(table: self::ENTITY_DATA_TABLE);
        $sql->set([
            'title' => $request->title,
            'icon_src' => $request->icon_src,
            'icon_alt' => $request->icon_alt,
            'body' => $request->body
        ]);
        $sql->where([self::ENTITY_PK => $id]);
        return (bool)$sql->update();
    }

    public function __construct(
        int|array $data = 0,
        #[ExpectedValues(self::VIEW_MODE_FULL, self::VIEW_MODE_TEASER)]
        protected string $view_mode = self::VIEW_MODE_FULL
    ) {
        if (is_array($data)) {
            $this->setLoadedData($data);
        } else {
            parent::__construct($data);
        }
    }

    /**
     * @param string $view_mode is name of view mode. Also named constants are available
     */
    public function setViewMode(
        #[ExpectedValues(
            self::VIEW_MODE_FULL,
            self::VIEW_MODE_TEASER
        )] string $view_mode
    ): self {
        $this->view_mode = $view_mode;
        return $this;
    }

    public function render()
    {
        $this->preprocessData();
        $this->tpl()->setName('content/skill--' . $this->view_mode);
        $this->tpl()->setId('skill-' . $this->id());
        foreach ($this->data as $key => $value) {
            $this->tpl()->set($key, $value);
        }
        return parent::render();
    }

    protected function preprocessData(): void
    {
        $this->data['body'] = new Markup($this->data['body'], CHARSET);
        $url = $this->url();
        $this->data['url'] = [
            'view' => $url ?? '#',
            'edit' => $url ? "{$url}/edit" : '#',
            'delete' => $url ? "{$url}/delete" : '#'
        ];
        $this->data['is_master'] = user()->verifyAccessLevel(User::ACCESS_LEVEL_MASTER);
        return;
    }

    public function url(): ?string
    {
        if ($this->id()) {
            return sprintf(self::URL_MASK, $this->id());
        }
        return null;
    }
}
