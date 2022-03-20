<?php

namespace Blog\Modules\Messenger;

use Blog\Modules\Template\Element;
use Blog\Client\User;

class Messenger extends \Blog\Modules\TemplateFacade\TemplateFacade
{
    public const SESSIONID = 'status-messages';
    public const SRCPATH = 'app/core/Modules/Messenger/src/';

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

    protected function set(string $text, array $options): void
    {
        $access_level = $options['access_level'] ?? null;
        // TODO: add all messages into events log
        if (
            app()->router()->isAjaxRequest()
            || (
                $access_level
                && !app()->user()->verifyAccessLevel($access_level)
            )
        ) {
            return;
        }
        $text = strip_tags($text);
        if (!empty($options['markup'] ?? [])) {
            foreach ($options['markup'] as $key => $value) {
                $text = str_replace("@{$key}", $value, $text);
            }
        }
        $message = [
            'prefix' => $options['prefix'] ?? null,
            'text' => new \Twig\Markup($text, CHARSET),
            'type' => $options['type'],
            'time' => time(),
            'class' => $options['class'] ?? null,
            'status' => 0
        ];
        session()->append(self::SESSIONID . '/list', $message);
        return;
    }

    public function debug(): void
    {
        $verbouse = false;
        $arguments = func_get_args();
        $i = array_search('--v', $arguments, true);
        if ($i || $i === 0) {
            unset($arguments[$i]);
            $verbouse = true;
        }
        $called_filename = strTrimServDir(debugFileCalled());
        foreach ($arguments as $arg) {
            $output = $verbouse ? debug($arg, '--v') : debug($arg);
            $options = [
                'type' => 'debug',
                'prefix' => $called_filename,
                'access_level' => User::ACCESS_LEVEL_ADMIN
            ];
            $this->set($output, $options);
        }
        return;
    }

    public function notice(string $text, ?array $markup = null, ?string $class = null, ?int $access_level = null): void
    {
        $options = [
            'type' => 'notice',
            'markup' => $markup,
            'class' => $class,
            'access_level' => $access_level
        ];
        $this->set($text, $options);
        return;
    }

    public function warning(string $text, ?array $markup = null, ?string $class = null, ?int $access_level = null): void
    {
        $options = [
            'type' => 'warning',
            'markup' => $markup,
            'class' => $class,
            'access_level' => $access_level
        ];
        $this->set($text, $options);
        return;
    }

    public function error(string $text, ?array $markup = null, ?string $class = null, ?int $access_level = null): void
    {
        $options = [
            'type' => 'error',
            'markup' => $markup,
            'class' => $class,
            'access_level' => $access_level
        ];
        $this->set($text, $options);
        return;
    }
}
