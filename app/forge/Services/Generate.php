<?php

namespace BlogForge\Services;

use Symfony\Component\Yaml\Yaml;

class Generate extends ServicePrototype implements ServiceInterface
{
    public function default(): void
    {
        $this->forge->setNotice("There is no default method for `generate` action");
    }

    public function run(?string $method): void
    {
        $method = lcfirst(pascalCase($method ?? ''));
        if (!method_exists($this, $method)) {
            forge()->setError("There is no available methods like `{$method}` for `generate` action.");
            return;
        }
        $this->{$method}();
        return;
    }

    public function env(): void
    {
        $content = file_get_contents(APPDIR . 'default/env.yml');
        $env_file = f('env')->content($content)->save();
        if ($env_file->exists()) {
            forge()->setSuccess("ENV file successfully generated in project root directory.");
        } else {
            forge()->setError("There is an error on generating env-file in project root directory.");
        }
        return;
    }
}
