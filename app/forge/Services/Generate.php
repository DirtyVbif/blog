<?php

namespace BlogForge\Services;

use Symfony\Component\Yaml\Yaml;

class Generate extends ServicePrototype implements ServiceInterface
{
    protected const PASSWORD_ALGO = PASSWORD_ARGON2I;

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

    public function password(): void
    {
        $password = forge()->arg(2);
        if (!$password) {
            forge()->setError("There is no password provided for encrypting");
            return;
        }
        // TODO: add salt for password
        $encrypt_password = password_hash($password, self::PASSWORD_ALGO);
        forge()->setSuccess(
            forge()->cli()->colorizeString('Password hash generated:', 'success')
        );
        forge()->setNotice(
            forge()->cli()->colorizeString($encrypt_password, 'attention')
        );
    }
}
