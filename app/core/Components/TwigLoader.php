<?php

namespace Blog\Components;

use \Twig\Loader\FilesystemLoader;
use \Twig\Extension\EscaperExtension as TwigEscaper;
use \Twig\Environment as Twig;
use \Twig\Extension\AbstractExtension;

trait TwigLoader
{
    protected Twig $twig;
    protected FilesystemLoader $twig_loader;

    private function twig_loader(): FilesystemLoader
    {
        if (!isset($this->twig_loader)) {
            $this->twig_loader = new FilesystemLoader(app()->config('twig')->templates, ROOTDIR);
        }
        return $this->twig_loader;
    }

    private function twig_set_base_safe_classes(): void
    {
        $safe_classes = app()->config('twig')->safe_classes;
        foreach ($safe_classes ?? [] as $class => $strategy) {
            $this->twig_get_escaper()?->addSafeClass($class, $strategy);
        }
    }

    private function twig_get_extension(string $name): ?AbstractExtension
    {
        $extensions = $this->twig()->getExtensions();
        return $extensions[$name] ?? null;
    }

    private function twig_get_escaper(): ?TwigEscaper
    {
        return $this->twig_get_extension('Twig\Extension\EscaperExtension');
    }

    public function twig(): Twig
    {
        if (!isset($this->twig)) {
            $config = app()->config('twig')->config;
            $this->twig = new Twig($this->twig_loader(), $config);
            $extensions = $this->twig->getExtensions();

            if (($config['debug'] ?? false) && !isset($extensions['Twig\Extension\DebugExtension'])) {
                $this->twig->addExtension(new \Twig\Extension\DebugExtension);
            }
            if (!isset($extensions['Twig\Extension\EscaperExtension'])) {
                $this->twig->addExtension(new TwigEscaper('html'));
            }
            $this->twig_set_base_safe_classes();
        }

        return $this->twig;
    }

    // public function twig_add_safe_class(string $class, array $strategy = ['html']): void
    // {
    //     $this->twig_get_escaper()?->addSafeClass($class, $strategy);
    //     return;
    // }

    // public function twig_add_path(string $directory, string $name): void
    // {
    //     $this->twig_loader = $this->twig()->getLoader();
    //     $this->twig_loader->addPath($directory, $name);
    // }
}
