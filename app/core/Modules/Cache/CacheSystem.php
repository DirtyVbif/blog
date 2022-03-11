<?php

namespace Blog\Modules\Cache;

use Blog\Client\User;
use Blog\Modules\FileSystem\Folder;

class CacheSystem implements \Blog\Components\AjaxModule
{
    protected array $entities = [];

    public function entity(string $name): CacheEntity
    {
        if (!isset($this->entities[$name])) {
            $this->entities[$name] = new CacheEntity($name);
        }
        return $this->entities[$name];
    }

    /**
     * @return int status code
     */
    public function clear(?string $cache_entity_name = null): array
    {
        if (!app()->user()->verifyAccessLevel(User::ACCESS_LEVEL_MASTER)) {
            return [
                'status' => 403,
                'output' => 'access denied'
            ];
        }
        if (app()->config('twig')->config['cache'] ?? false) {
            $folder = new Folder(app()->twig()->getCache());
            $folder->clear();
        }
        foreach (app()->config()->cache as $name => $data) {
            $this->entity($name)->clear();
        }
        return [
            'status' => 200,
            'output' => 'cache cleared'
        ];
    }

    public function ajaxRequest(): array
    {
        $response = [
            'output' => null,
            'status' => 200
        ];
        $method = $_GET['fn'] ?? null;
        $key = $_GET['key'] ?? null;
        if (is_null($method) || !method_exists($this, $method)) {
            $response['status'] = 404;
            return $response;
        }
        $result = $this->$method($key);
        $result['output'] ??= null;
        $result['status'] ??= $response['status'];
        return $result;
    }
}
