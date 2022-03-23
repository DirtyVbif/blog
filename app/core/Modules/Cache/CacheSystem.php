<?php

namespace Blog\Modules\Cache;

use Blog\Client\User;
use Blog\Mediators\AjaxResponse;
use Blog\Modules\FileSystem\Folder;

class CacheSystem implements \Blog\Components\AjaxModule
{
    protected array $entities = [];

    public function entity(string $name): CacheEntity|CacheSql
    {
        if (!isset($this->entities[$name])) {
            $this->entities[$name] = preg_match('/^sql$/i', $name) ? new CacheSql : new CacheEntity($name);
        }
        return $this->entities[$name];
    }

    public function sql(): CacheSql
    {
        if (!isset($this->entities['sql'])) {
            $this->entities['sql'] = new CacheSql;
        }
        return $this->entities['sql'];
    }

    /**
     * @return int status code
     */
    public function clear(?string $cache_entity_name = null): AjaxResponse
    {
        $response = new AjaxResponse('cache cleared');
        if (!app()->user()->verifyAccessLevel(User::ACCESS_LEVEL_MASTER)) {
            $response->setCode(403);
            $response->setResponse('access denied');
            return $response;
        }
        if (app()->config('twig')->config['cache'] ?? false) {
            $folder = new Folder(app()->twig()->getCache());
            $folder->clear();
        }
        foreach (app()->config()->cache as $name => $data) {
            $this->entity($name)->clear();
        }
        return $response;
    }

    public function ajaxRequest(): AjaxResponse
    {
        $response = new AjaxResponse();
        $method = $_GET['fn'] ?? null;
        $key = $_GET['key'] ?? null;
        if (is_null($method) || !method_exists($this, $method)) {
            $response->setCode(404);
            return $response;
        }
        return $this->$method($key);
    }
}
