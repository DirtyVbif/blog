<?php

namespace Blog\Modules\FileSystem;

class File
{
    protected const LOGID = 'Filesystem';
    protected const DEFAULT_FILE_PERMISSIONS = 0644;

    protected string $dir;
    protected Folder $folder;
    protected string $name;
    protected string $extension;
    protected string $content;
    protected string $real_content;
    protected bool $exists = false;
    protected bool $rewrited = false;
    protected int $permissions = 0644;

    /**
     * @var resource|false a file pointer resource on success, or false on error
     */
    protected $handle;

    public function __construct(string $name, ?string $directory = null, ?string $extension = null)
    {
        $this->dir($directory);
        $this->extension($extension);
        $this->name($name);
        $this->checkExistingFile();
        return $this;
    }

    private function checkExistingFile(): self
    {
        $this->exists = file_exists($this->filename());
        return $this;
    }

    /**
     * @return string|null|self
     */
    public function name(?string $name = null)
    {
        if (!$name) {
            return $this->name ?? null;
        }
        if (preg_match('/(\\\|\/)+/', $name)) {
            $parts = preg_split('/(\\\|\/)+/', $name);
            foreach ($parts as $i => $part) {
                if (!preg_replace('/\s+/', '', $part)) {
                    unset($parts[$i]);
                }
            }
            $name = array_pop($parts);
            if (!empty($parts)) {
                $directory = implode('/', $parts);
                $this->dir($directory);
            }
        }
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null|self
     */
    public function extension(?string $extension = null)
    {
        if (is_null($extension)) {
            return $this->extension ?? null;
        }
        $extension = preg_replace('/^\.+/', '', $extension);
        if ($extension !== $this->extension() && $this->exists) {
            rename($this->filename(), strSuffix($this->name(), '.' . $extension));
        }
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param string $directory [optional] path to the folder. Leading and trailing slashes will be removed
     * 
     * @return string|null|self in case when returns current directory returns path without leading and trailing slashes.
     */
    public function dir(?string $directory = null)
    {
        if (is_null($directory)) {
            return $this->dir ?? null;
        } else if (!$directory) {
            $directory = '';
        }
        $this->folder = new Folder($directory);
        $this->dir = $this->folder->path();
        return $this;
    }

    /**
     * @param string $content [optional] new content for file that can be saved via @method save()
     * @param bool $read [optional] if content for file is not setted then will try to read content from existing file if `TRUE`
     */
    public function content(?string $content = null, bool $read = true): string|self
    {
        if (is_null($content)) {
            if ($read) {
                $this->read();
            }
            return $this->content ?? '';
        }
        $this->content = $content;
        $this->rewrited = true;
        return $this;
    }

    public function addContent(string $content): self
    {
        if (!isset($this->content)) {
            $this->content($content);
        } else {
            $this->content .= $content;
        }
        return $this;
    }

    public function filename(): string
    {
        $dir = strSuffix($this->dir(), '/');
        return $this->extension() ?
            $dir . strSuffix($this->name(), '.' . $this->extension())
            : $dir . $this->name();
    }

    public function save(): self
    {
        $file = $this->filename();
        if (!file_exists($this->dir())) {
            $this->folder->create();
        }
        $this->handle = fopen($file, 'w+');
        if (!$this->handle) {
            pre("Can't open/create \"{$file}\" file.");
        } else {
            fwrite($this->handle, $this->content(read: false));
            if ($this->exists = fclose($this->handle)) {
                chmod($file, $this->permissions());
                $this->rewrited = false;
                $this->real_content = $this->content();
            } else {
                pre("Failed to save file \"{$file}\"", $this->handle);
            }
        }
        return $this;
    }

    public function realContent(): string
    {
        if ($this->exists) {
            return isset($this->real_content) ? $this->real_content : $this->real_content = file_get_contents($this->filename());
        }
        return '';
    }

    public function read(): self
    {
        if ($this->rewrited || !isset($this->content)) {
            $this->content = $this->realContent();
            $this->rewrited = false;
        }
        return $this;
    }

    public function del(): bool
    {
        $file = $this->filename();
        if ($this->exists()) {
            $this->exists = false;
            consoleLog(self::LOGID, "File {$file} deleted successfully.");
            return unlink($file);
        } else {
            consoleLog(self::LOGID, "File {$file} can't be deleted. It's unexisting.");
        }
        return false;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function permissions(?int $permissions = null): self|int
    {
        if (is_null($permissions)) {
            return $this->permissions ?? self::DEFAULT_FILE_PERMISSIONS;
        }
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * Decode current content and return as array or object
     */
    public function json_decode(bool $associative = true): array|object
    {
        return json_decode($this->content(), $associative);
    }

    /**
     * Encode current content and return result as decoded json string on success or false on failure
     */
    public function json_encode(): string|false
    {
        return json_encode($this->content());
    }
}
