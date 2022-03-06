<?php

namespace BlogForge;

use BlogForge\Services\ServiceFactory;

class Forge
{
    use \Blog\Components\Singletone;
    
    private array $outputs = [];
    private Components\CommandLineInterface $cli;

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
        $this->setNotice('Some notice default output string lorem ipsum dolor bla bla bla');
        return;
    }

    public function setError(string $text): void
    {
        $this->outputs[] = $this->cli()->outputError($text);
        return;
    }

    public function setNotice(string $text): void
    {
        $this->outputs[] = $this->cli()->outputNotice($text);
        return;
    }

    public function setSuccess(string $text): void
    {
        $this->outputs[] = $this->cli()->outputSuccess($text);
        return;
    }

    public function cli(): Components\CommandLineInterface
    {
        if (!isset($this->cli)) {
            $this->cli = new Components\CommandLineInterface;
        }
        return $this->cli;
    }
}
