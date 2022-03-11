<?php

namespace Blog\Modules\Messenger;

use Blog\Modules\Template\Element;

class Logger extends \Blog\Modules\TemplateFacade\TemplateFacade
{
    public const TABLE = 'log';
    protected bool $access;
    protected array $items = [];
    protected bool $library_used = false;

    /**
     * @return Element $tpl
     */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            app()->twig_add_namespace(Messenger::SRCPATH, 'messenger');
            $tpl = new Element;
            $tpl->setNamespace('messenger');
            $tpl->setName('log');
            $tpl->setId('system-log');
            $this->tpl = $tpl;
        }
        return $this->tpl;
    }

    public function render()
    {
        if (!$this->access() || empty($this->items)) {
            return '';
        }
        $this->tpl()->set('items', $this->items);
        return parent::render();
    }

    public function console(string $type, string $message): void
    {
        $this->useLibrary();
        if (!isset($this->items[$type])) {
            $this->items[$type] = [];
        }
        $this->items[$type][] = $message;
        return;
    }

    protected function useLibrary(): void
    {
        if (!$this->library_used) {
            app()->library('system-log')->use();
            $this->library_used = true;
        }
        return;
    }

    protected function access(): bool
    {
        if (!isset($this->access)) {
            $this->access = app()->user()->isMaster();
        }
        return $this->access;
    }

    public function log(string $type, string $output, array $data = []): void
    {
        $sql = sql_insert(self::TABLE);
        if (!empty($data)) {
            $data = json_encode($data);
        } else {
            $data = null;
        }
        $sql->set(
            [$type, $output, $data],
            ['type', 'output', 'data']
        );
        $sql->exe();
    }
}