<?php

namespace Blog\Modules\Cache;

use Blog\Modules\FileSystem\File;
use Blog\Modules\FileSystem\Folder;

class CacheSql
{
    public const DATAFILE = 'data.json';
    public const LOGID = 'SQL-Cache';

    protected array $config;
    protected int $lifetime;
    protected bool $status;
    /**
     * Path to the cache directory with trailing slash `/`
     */
    protected string $directory_path;
    protected Folder $folder;
    protected File $data_file;

    public function __construct()
    {
        // set sql cache config
        $this->prepareConfing();
        // check cache directory
        $this->prepareDirectory();
        // check cache data file
        $this->prepareDataFile();
    }

    protected function cfg(string $key)
    {
        return $this->config[$key] ?? null;
    }

    protected function prepareConfing(): void
    {
        $this->config = app()->config('cache')->sql ?? ['status' => false, 'lifetime' => 0];
        $this->lifetime = $this->cfg('lifetime');
        $this->status = $this->cfg('status');
        $this->directory_path = app()->config('cache_directory') . 'sql/';
    }

    protected function prepareDirectory(): void
    {
        if (!$this->status()) {
            return;
        } else if (!$this->folder()->exists()) {
            $this->folder()->create();
        }
        return;
    }

    protected function prepareDataFile(): void
    {
        if (!$this->status()) {
            return;
        } else if (!$this->fdata()->exists()) {
            $this->setData([]);
        }
        return;
    }

    public function status(): bool
    {
        return $this->status ?? false;
    }

    public function lifetime(): int
    {
        return $this->lifetime ?? 0;
    }

    protected function folder(): Folder
    {
        if (!isset($this->folder)) {
            $this->folder = new Folder($this->directory_path);
        }
        return $this->folder;
    }

    protected function fdata(): File
    {
        if (!isset($this->data_file)) {
            $this->data_file = new File(self::DATAFILE, $this->folder()->path());
        }
        return $this->data_file;
    }

    protected function exportData(array $data): string
    {
        return json_encode($data);
    }

    protected function importData(string $json_data): array
    {
        return json_decode($json_data, true);
    }

    protected function getData(): array
    {
        return $this->importData(
            $this->fdata()->realContent()
        );
    }

    protected function setData(array $data): void
    {
        $this->fdata()->content(
            $this->exportData($data),
            false
        )->save();
        return;
    }

    protected function cacheNameFormRequest(string $sql_request): string
    {
        return md5($sql_request);
    }
    
    /**
     * Store new cache data and cache file
     * 
     * @param string $sql_request - is raw SQL SELECT request
     * @param array $data - is result of SQL SELECT query
     */
    public function set(string $sql_request, array $data, array $tables): self
    {
        $cache_name = $this->cacheNameFormRequest($sql_request);
        $timestamp = time();
        f($cache_name, $this->folder()->path())
            ->content(
                $this->exportData($data),
                false
            )->save();
        $data = $this->getData();
        $data[$cache_name] = [
            'request' => $sql_request,
            'timestamp' => $timestamp,
            'tables' => $tables
        ];
        $this->setData($data);
        return $this;
    }

    /**
     * @param string $sql_request - is raw SQL SELECT request
     */
    public function get(string $sql_request): array|false
    {
        if (!$this->status()) {
            return false;
        }
        $cache_name = $this->cacheNameFormRequest($sql_request);
        if (!$this->validateCache($cache_name)) {
            consoleLog('SQL-Cache', "Cache lifetime expired for SQL REQUEST {$sql_request}.");
            return false;
        }
        return $this->getCache($cache_name);
    }

    /**
     * Check if cache is exists and it is still actual
     */
    protected function validateCache(string $cache_name): bool
    {
        $data = $this->getData();
        if (!isset($data[$cache_name])) {
            return false;
        }
        $cache_timestamp = $data[$cache_name]['timestamp'] ?? 0;
        $cache_lifetime = time() - $cache_timestamp;
        consoleLog('SQL-Cache', "Validating timestamp for cache: LIFETIME={$this->lifetime()}; CACHE_LIFETIME={$cache_lifetime};");
        return $this->lifetime() > $cache_lifetime;
    }

    /**
     * Get cache data
     */
    protected function getCache(string $cache_name): array|false
    {
        $cache_file = $this->folder()->path() . $cache_name;
        if (!file_exists($cache_file)) {
            return false;
        }
        return $this->importData(
            file_get_contents($cache_file)
        );
    }

    public function markupToDateTables(array $tables): void
    {
        if (!$this->status()) {
            return;
        }
        $cache_files = [];
        foreach ($this->getData() as $cache_name => $data) {
            foreach ($tables as $table) {
                if (!in_array($table, $data['tables'])) {
                    continue;
                }
                $cache_files[$cache_name] = true;
            }
        }
        $this->clear($cache_files);
        return;
    }

    public function clear(?array $cache_files = null): void
    {
        if (!$this->status()) {
            return;
        } else if (is_null($cache_files)) {
            $this->clearCache();
            return;
        } else if (!empty($cache_files)) {
            $data = $this->getData();
            foreach ($cache_files as $cache_name => $bool) {
                consoleLog(self::LOGID, "Deleting cache {$cache_name}.");
                unset($data[$cache_name]);
                unlink($this->folder()->path() . $cache_name);
            }
            $this->setData($data);
        }
        return;
    }

    protected function clearCache(): void
    {
        $this->folder()->clear();
        return;
    }
}
