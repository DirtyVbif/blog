<?php

namespace BlogForge;

use BlogForge\Services\ServiceFactory;

class Forge
{
    use \Blog\Components\Singletone;
    
    private array $outputs = [];
    private Components\CommandLineInterface $cli;
    private array $args;

    public function init(): void
    {
        $this->args = $GLOBALS['argv'];
        if ($this->arg(1)) {
            ServiceFactory::callback($this->arg(1));
        } else {
            $this->defaults();
        }
        return;
    }

    public function arg(int $number): ?string
    {
        return $this->args[max(1, $number)] ?? null;
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
