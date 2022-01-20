<?php

namespace Blog\Modules\Messenger;

use Blog\Modules\Template\Element;
use Twig\Markup;

class Messenger extends \Blog\Modules\TemplateFacade\TemplateFacade
{
    public const SESSIONID = 'status-messages';
    public const SRCPATH = 'app/core/Modules/Messenger/src/';
    public const ACCESS_LEVEL_ALL = 0;
    public const ACCESS_LEVEL_ADMIN = 1;

    /**
     * @return Element $tpl
     */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            app()->twig_add_namespace(self::SRCPATH, 'messenger');
            $tpl = new Element;
            $tpl->setNamespace('messenger');
            $tpl->setName('messages');
            $tpl->setId('messenger')
                ->setAttr('role', 'status')
                ->addClass('status-container');
            $this->tpl = $tpl;
        }
        return $this->tpl;
    }

    public function render()
    {
        $messages = $this->prepareMessages();
        if (empty($messages)) {
            return '';
        }
        $this->tpl()->set('items', $messages);
        return parent::render();
    }

    protected function prepareMessages(): array
    {
        $sessid = self::SESSIONID . '/list';
        $list = session()->get($sessid) ?? [];
        foreach ($list as $i => &$message) {
            if ($message['status'] ?? false) {
                unset($list[$i]);
                session()->unset("{$sessid}/{$i}");
            } else {
                $message['status'] = 1;
                session()->set("{$sessid}/{$i}/status", $message['status']);
            }
        }
        return $list;
    }

    protected function set(string $text, string $type, $prefix = null, int $access = self::ACCESS_LEVEL_ALL): void
    {
        // TODO: verify message access level
        $message = [
            'prefix' => $prefix,
            'text' => $text,
            'type' => $type,
            'time' => time(),
            'status' => 0
        ];
        session()->append(self::SESSIONID . '/list', $message);
        return;
    }

    public function debug(string $text, int $access = self::ACCESS_LEVEL_ALL): void
    {
        $called_filename = strTrimServDir(debugFileCalled());
        $this->set($text, 'debug', $called_filename);
        return;
    }

    public function notice(string $text, int $access = self::ACCESS_LEVEL_ALL): void
    {
        $this->set($text, 'notice');
        return;
    }

    public function warning(string $text, int $access = self::ACCESS_LEVEL_ALL): void
    {
        $this->set($text, 'warning');
        return;
    }

    public function error(string $text, int $access = self::ACCESS_LEVEL_ALL): void
    {
        $this->set($text, 'error');
        return;
    }
}
