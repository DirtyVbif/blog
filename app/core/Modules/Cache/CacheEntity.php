<?php

namespace Blog\Modules\Cache;

use Blog\Modules\FileSystem\File;
use Blog\Modules\FileSystem\Folder;

class CacheEntity
{
    protected const DIR = 'cache/';
    protected const DATFILE = 'cache.module';
    protected const DEFAULT_DIR_PERMISIONS = 0755;
    protected const TEST = 'test';

    protected array $config;
    protected int $lifetime;
    protected bool $status;
    protected array $cache_data = [];
    protected bool $initialized = false;
    protected Folder $directory;
    protected File $data_file;

    public function __construct(protected string $name)
    {
        $this->config = app()->config('cache')->$name ?? [];
        $this->lifetime($this->cfg('lifetime'))
            ->status($this->cfg('status'));

        $this->initialize();
        return $this;
    }

    public function cfg(string $key)
    {
        return $this->config[$key] ?? null;
    }

    protected function initialize(): self
    {
        if (!$this->status()) {
            return $this;
        }
        $this->prepareCacheDir();
        $this->prepareCacheDataFile();
        $this->initialized = true;
        return $this;
    }

    protected function prepareCacheDir(): self
    {
        $dir = app()->config('cache_directory') . strSuffix($this->name, '/');
        $this->dir($dir);
        $this->dir()->create();
        return $this;
    }

    protected function prepareCacheDataFile(): self
    {
        $this->cache_data['list'] = $this->dir()->scan();
        $this->cache_data['data'] = $this->getDataFileContent();
        return $this;
    }

    protected function getRequestCacheName(string $request): string
    {
        return md5($request);
    }

    public function name(?string $name = null): string|self
    {
        if (is_null($name)) {
            return $this->name;
        }
        $this->name = $name;
        return $this;
    }

    public function lifetime(?int $lifetime = null): int|self
    {
        if (is_null($lifetime)) {
            return $this->lifetime;
        }
        $this->lifetime = $lifetime;
        return $this;
    }

    public function status(?bool $status = null): bool|self
    {
        if (is_null($status)) {
            return $this->status;
        }
        $this->status = $status;
        return $this;
    }

    /**
     * Get data file handler
     */
    protected function dataFile(): File
    {
        if (!isset($this->data_file)) {
            $this->data_file = f(self::DATFILE, $this->dir()->path());
        }
        if (!$this->data_file->exists()) {
            $content = $this->varphpstr([]);
            $this->data_file->content($content)->save();
        }
        return $this->data_file;
    }

    public function getDataFileContent(): array
    {
        $data = include $this->dataFile()->filename();
        return $data;
    }

    /**
     * get @var self::$cache_data or exact @var self::$cache_data variable as @var object
     */
    public function cacheData(?string $key = null)
    {
        if (!is_null($key)) {
            return $this->cache_data[$key] ?? null;
        }
        return (object)$this->cache_data;
    }

    /**
     * @param bool|self $status
     */
    public function dir(?string $directory = null): Folder
    {
        if (!is_null($directory)) {
            $this->directory = new Folder($directory);
        }
        return $this->directory;
    }

    /**
     * Get cached content if it exists by cache name
     * 
     * @param string $name cache unique name
     */
    public function get(string $request): ?array
    {
        $name = $this->getRequestCacheName($request);
        if (!$this->status() || !in_array($name, $this->cacheData()->list)) {
            return null;
        }
        $cache_file = f($name, $this->dir()->path());
        $content = include $cache_file->filename();
        $content = $this->validateCacheLifetime($content);
        if (!$content) {
            $cache_file->del();
        } else if (($content['request'] ?? null) === $request) {
            $content = $content['query'] ?? null;
        } else {
            $content = null;
        }
        return $content;
    }

    protected function validateCacheLifetime(array $cache): ?array
    {
        $lifetime = time() - ($cache['timestamp'] ?? 0);
        if ($lifetime > $this->lifetime()) {
            return null;
        }
        return $cache;
    }

    /**
     * Store new cache data into cache file
     */
    public function set(string $request, $query, array $tbls): self
    {
        $cache_name = $this->getRequestCacheName($request);
        $time = time();
        $cache_data = $data_info = [
            'request' => $request,
            'timestamp' => $time
        ];
        $cache_data['query'] = $query;
        $data_info['tables'] = $tbls;
        $cache_file = f($cache_name, $this->dir()->path());
        $content = $this->varphpstr($cache_data);
        $cache_file->content($content)->save();
        $this->storeCacheFileData($cache_name, $data_info);
        return $this;
    }

    public function storeCacheFileData(string $cache_name, array $data_info): self
    {
        $this->cache_data['data'][$cache_name] = $data_info;
        $this->updateDataFile();
        return $this;
    }

    protected function updateDataFile(): self
    {
        $content = $this->varphpstr($this->cacheData()->data);
        $this->dataFile()->content($content)->save();
        return $this;
    }

    /**
     * Converts any Variable to PHP String of it's representation
     */
    protected function varphpstr($variable): string
    {
        $content = '<?php return ' . var_export($variable, true) . '; ?>';
        if ($this->cfg('minimized')) {
            $content = preg_replace('/[\n\t\r\s]+/', ' ', $content);
        }
        return $content;
    }

    public function markupToUpdate(array $tables): self
    {
        $outdated_cache = [];
        foreach ($this->getDataFileContent() as $cache_name => $data) {
            foreach ($tables as $t) {
                if (in_array($t, $data['tables'])) {
                    $outdated_cache[$cache_name] = $cache_name;
                }
            }
        }
        if (!empty($outdated_cache)) {
            $this->clear($outdated_cache);
        }
        return $this;
    }

    public function clear(array $files = []): self
    {
        if (!$this->status()) {
            return $this;
        } else if (empty($files)) {
            $this->dataFile()->del();
            $this->dir()->clear();
            $this->initialize();
            return $this;
        }
        foreach ($files as $file) {
            f($file, $this->dir()->path())->del();
            unset($this->container['data'][$file]);
        }
        $this->updateDataFile();
        return $this;
    }
}
