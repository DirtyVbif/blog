<?php

namespace Blog\Components;

use Blog\Modules\FileSystem\File;
use Symfony\Component\Yaml\Yaml;

trait BlogConfig
{
    /**
     * @var array<string, string,array<string, mixed>> $config
     */
    private array $config;
    private bool $config_loaded = false;
    private bool $env_loaded = false;
    private object $manifest;
    private File $manifest_file;
    private array $env;

    private function loadConfig(): void
    {
        // load config from file
        $this->config = require_once APPDIR . 'blog.config.php';
        // initialize config as loaded
        $this->config_loaded = true;
        return;
    }

    /**
     * @return object if @var `string $config_name` is null;
     * @return mixed if @var `string $config_name` specified for config name
     * @return null if no config for provided @var `string $config_name`
     */
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

    public function manifest(): object
    {
        if (!isset($this->manifest_file)) {
            $this->manifest_file = f('manifest.json', PUBDIR);
        }
        if (!isset($this->manifest)) {
            $this->manifest = $this->manifest_file->json_decode(false);
        }
        return $this->manifest;
    }
}
