<?php

namespace BlogForge;

use BlogForge\Services\ServiceFactory;

class Forge
{
    use \Blog\Components\Singletone;
    
    private array $outputs = [];

    public function init(): void
    {
        $args = $GLOBALS['argv'];
        $argument = $args[1] ?? null;
        if ($argument) {
            ServiceFactory::callback($argument);
        } else {
            $this->defaults();
        }
        return;
    }

    public function response(): string
    {
        return implode(PHP_EOL, $this->outputs);
    }

    private function defaults(): void
    {
        $this->setNotice('Output available commands:');
        return;
    }

    public function setError(string $text): void
    {
        $this->setOutput($text, 31);
        return;
    }

    public function setNotice(string $text): void
    {
        $this->setOutput($text);
        return;
    }

    public function setSuccess(string $text): void
    {
        $this->setOutput($text, 32);
        return;
    }

    private function setOutput(string $text, int $code = 0): void
    {
        $this->outputs[] = $code ? "\e[{$code}m{$text}\e[0m" : $text;
    }
}
