<?php

namespace Blog\Components;

use Symfony\Component\Yaml\Yaml;

trait BlogConfig
{
    private array $config;
    private bool $config_loaded = false;
    private bool $env_loaded = false;
    private array $env;

    private function loadConfig(): void
    {
        // load config from file
        $this->config = require_once APPDIR . 'blog.config.php';
        // initialize config as loaded
        $this->config_loaded = true;
        return;
    }

    public function config(?string $config_name = null)
    {
        if (!$this->config_loaded) {
            $this->loadConfig();
        }
        if (is_null($config_name)) {
            return (object)$this->config;
        }
        $result = $this->config[$config_name] ?? null;
        return is_array($result) ? (object)$result : $result;
    }

    public function env(): \stdClass
    {
        if (!$this->env_loaded) {
            $this->env = Yaml::parseFile(ROOTDIR . 'env');
            $this->env_loaded = true;
        }
        return (object)$this->env;
    }
}
