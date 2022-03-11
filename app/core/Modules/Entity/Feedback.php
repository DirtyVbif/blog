<?php

namespace Blog\Modules\Entity;

use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\Template\Element;
use Blog\Request\RequestPrototype;
use JetBrains\PhpStorm\ExpectedValues;

class Feedback extends EntityPrototype
{
    public const ENTITY_DATA_TABLE = 'entities_feedback_data';
    public const ENTITY_DATA_COLUMNS = ['subject', 'message', 'headers', 'result'];
    /** @var int entity type id (etid) specified in entities_types table */
    public const ENTITY_TYPE_ID = 2;
    public const VIEW_MODE_FULL = 'full';
    public const URL_MASK = '/feedbacks/%d';

    public static function getSqlTableName(): array|string
    {
        return ['f' => self::ENTITY_DATA_TABLE];
    }

    public static function getSqlTableColumns(): array
    {
        return ['f' => self::ENTITY_DATA_COLUMNS];
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
     * @return Feedbacks[]
     */
    public static function loadList(array $options): array
    {
        $feedbacks = [];
        $sql = self::sql();
        $sql->where(['et.etid' => self::ENTITY_TYPE_ID]);
        $sql->order('e.created', $options['order'] ?? 'ASC');
        $sql->limit($options['limit'] ?? null);
        $sql->limitOffset($options['offset'] ?? null);
        $view_mode = $options['view_mode'] ?? self::VIEW_MODE_FULL;
        foreach ($sql->all() as $feedback) {
            $feedbacks[$feedback['id']] = new self($feedback, $view_mode);
        }
        return $feedbacks;
    }
    
    /**
     * @param \Blog\Request\FeedbackRequest $request
     */
    public static function create(RequestPrototype $request, ?array $data = null): bool
    {
        /** @var \Blog\Modules\Mailer\EMail $email */
        $email = $data['email'];
        /** @var bool $status */
        $status = $data['status'];
        $message = "{$request->name} ({$email->getFrom()}): {$request->subject}";
        sql()->startTransation();
        $sql = sql_insert('entities');
        $sql->set([self::ENTITY_TYPE_ID], ['etid']);
        $rollback = true;
        if ($entity_id = $sql->exe()) {
            $sql = sql_insert(self::ENTITY_DATA_TABLE);
            $sql->set(
                [$entity_id, $email->getSubject(), $message, json_encode($email->getHeaders()), (int)$status, user()->ip()],
                ['eid', 'subject', 'message', 'headers', 'result', 'ip']
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
     * @param int|array $data is an id of article that must be loaded or already loaded article data.
     * If integer id provided as `int $data` then article will be automatically loaded from storage.
     * Else if array with article data provided as `array $data` then article wouldn't be loaded from storage and accept provided data.
     */
    public function __construct(
        int|array $data,
        #[ExpectedValues([self::VIEW_MODE_FULL])]
        string $view_mode = self::VIEW_MODE_FULL
    ) {
        if (is_array($data)) {
            $this->setLoadedData($data);
        } else {
            parent::__construct($data);
        }
        $this->setViewMode($view_mode);
    }

    public function load(?int $id = null, bool $load_comments = true): void
    {
        if (!is_null($id)) {
            $this->id = $id;
        }
        if ($this->id()) {
            $sql = self::sql();
            $sql->where(condition: ['e.eid' => $this->id()]);
            $this->setLoadedData($sql->all());
        }
        return;
    }

    protected function setLoadedData(array $data): void
    {
        if (!arrayIsFlat($data)) {
            // TODO: set throwable error
            pre([
                'error' => 'Provided comment data is not compatable with Entity/Comment::class',
                'data' => $data
            ]);
            die;
        }
        $this->data = $data;
        return;
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
        $this->preprocessData();
        $this->tpl()->setName('content/feedback--' . $this->view_mode);
        $this->tpl()->setId('feedback-' . $this->id());
        foreach ($this->data as $key => $value) {
            $this->tpl()->set($key, $value);
        }
        if (!$this->status()) {
            $this->tpl()->addClass('unpublished');
        }
        return parent::render();
    }

    protected function preprocessData(): void
    {
        if (!empty($this->data)) {
            $this->data['date'] = new DateFormat($this->data['created'], DateFormat::DETAILED);
            $this->data['headers'] = json_decode($this->data['headers'], true);
        }
        return;
    }

    public function status(): bool
    {
        $status = (int)($this->data['result'] ?? 0);
        return $status;
    }

    /**
     * @param string $view_mode is name of view mode. Also named constants are available:
     */
    public function setViewMode(
        #[ExpectedValues([self::VIEW_MODE_FULL])]
        string $view_mode
    ): self {
        $this->view_mode = $view_mode;
        return $this;
    }

    public function url(): ?string
    {
        if ($this->id()) {
            return sprintf(self::URL_MASK, $this->id());
        }
        return null;
    }
}
