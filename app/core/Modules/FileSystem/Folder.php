<?php

namespace Blog\Modules\FileSystem;

class Folder
{
    protected const DEFAULT_PERMISSIONS = 0755;

    protected string $path;
    protected bool $exists;
    protected int $permissions;

    public function __construct(string $path) {
        ffpath($path);
        $this->path = strSuffix($path, '/');
        $this->checkStatus();
    }

    protected function checkStatus(): void
    {
        $this->exists = is_dir($this->path);
        return;
    }

    public function permissions(?int $permissions = null): int|self
    {
        if (is_null($permissions)) {
            return $this->permissions ?? self::DEFAULT_PERMISSIONS;
        }
        $this->permissions = $permissions;
        return $this;
    }
    
    public static function chmod(string $directory, bool $recursively = false, int $permissions = self::DEFAULT_PERMISSIONS): void
    {
        if ($recursively) {
            $parts = preg_split('/[\/\\\]+/', $directory);
            $path = '';
            foreach ($parts as $part) {
                if (!$part || preg_match('/\.+/', $part)) {
                    continue;
                }
                $path .= $part . '/';
                chmod($path, $permissions);
            }
        } else {
            chmod($directory, $permissions);
        }
        return;
    }

    /**
     * Returns current path to folder with trailing slash `/`
     */
    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return $this->exists ?? false;
    }

    public function create(): bool
    {
        if ($this->exists()) {
            return $this->exists();
        }
        $this->exists = mkdir($this->path(), $this->permissions(), true);
        return $this->exists();
    }

    public function scan(): ?array
    {
        if (!$this->exists()) {
            return null;
        }
        $list = scandir($this->path());
        foreach ($list as $i => $filename) {
            if (preg_match('/^\.+$/', $filename) || !$filename) {
                unset($list[$i]);
            }
        }
        return $list;
    }

    /**
     * Removes all files in current folder
     * 
     * @return int $count of deleted files
     */
    public function clear(): void
    {
        foreach ($this->scan() as $filename) {
            self::clearRecursively(
                $this->path() . $filename
            );
        }
        return;
    }

    public static function clearRecursively(string $path): void
    {
        ffpath($path);
        if (is_dir($path)) {
            $path = strSuffix($path, '/');
            foreach (scandir($path) as $filename) {
                if (preg_match('/^\.+$/', $filename) || !$filename) {
                    continue;
                }
                self::clearRecursively($path . $filename);
            }
            rmdir($path);
        } else {
            unlink($path);
        }
        return;
    }
}