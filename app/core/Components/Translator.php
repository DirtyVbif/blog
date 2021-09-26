<?php

namespace Blog\Components;

use Symfony\Component\Yaml\Yaml;

trait Translator
{
    protected array $translations;
    protected bool $translations_checked = false;

    public function translate(string $text): string
    {
        $this->checkTranslations();
        return $this->translations[$text] ?? $text;
    }

    public function langcode(): string
    {
        return app()->router()->getLangcode();
    }

    protected function checkTranslations(): void
    {
        if (!isset($this->translations) && !$this->translations_checked) {
            $file = APPDIR . 'translations/' . $this->langcode() . '/list.yml';
            if (!file_exists($file)) {
                $this->translations_checked = true;
                $this->translations = [];
                return;
            }
            $this->translations = Yaml::parseFile($file);
        }
    }
}
