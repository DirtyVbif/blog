<?php

namespace Blog\Components;

trait BlogConfig
{
    private array $config;
    private bool $config_loaded = false;

    private function loadConfig(): void
    {
        // load config from file
        $this->config = require_once APPDIR . 'blog.config.php';
        // initialize config as loaded
        $this->config_loaded = true;
        return;
    }

    public function config(?string $config_name = null): ?\stdClass
    {
        if (!$this->config_loaded) {
            $this->loadConfig();
        }
        if (is_null($config_name)) {
            return (object)$this->config;
        }
        return isset($this->config[$config_name]) ? (object)$this->config[$config_name] : null;
    }
}
