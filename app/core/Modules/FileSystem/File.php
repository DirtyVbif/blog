<?php

namespace Blog\Modules\FileSystem;

class File
{
    protected const DEFAULT_FILE_PERMISIONS = 0644;

    protected string $dir;
    protected string $name;
    protected string $extension;
    protected string $content;
    protected bool $created = false;
    protected int $permissions = 0644;

    /**
     * @var resource|false a file pointer resource on success, or false on error
     */
    protected $handle;

    public function __construct(string $name, string $directory = '.', ?string $extension = null)
    {
        $this
            ->name($name)
            ->dir($directory)
            ->extension($extension);
        $this->checkExistingFile();
        return $this;
    }

    private function checkExistingFile(): self
    {
        $this->created = file_exists($this->filename());
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
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null|self
     */
    public function extension(?string $extension = null)
    {
        if (!$extension) {
            return $this->extension ?? null;
        }

        $extension = preg_replace('/^\.+/', '', $extension);
        if ($extension !== $this->extension && $this->created) {
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
            $directory = '.';
        }
        $this->dir = strSuffix($directory, '/', true);
        return $this;
    }

    /**
     * @return string|self
     */
    public function content(?string $content = null)
    {
        if (is_null($content)) {
            return $this->content ?? '';
        }
        $this->content = $content;
        return $this;
    }

    public function addContent(string $content): self
    {
        $this->content .= $content;
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
        $this->handle = fopen($file, 'w');
        if (!$this->handle) {
            pre("Can't open/create $file file.");
        } else {
            fwrite($this->handle, $this->content());
            $this->created = true;
            fclose($this->handle);
            chmod($file, $this->permissions());
        }
        return $this;
    }

    public function realContent()
    {
        if ($this->created) {
            return file_get_contents($this->filename());
        }
        return false;
    }

    public function del(): void
    {
        if ($this->created) {
            $this->created = false;
            $file = $this->filename();
            unlink($file);
        }
        return;
    }

    public function exists(): bool
    {
        return $this->created;
    }

    public function permissions(?int $permissions = null): self|int
    {
        if (is_null($permissions)) {
            return $this->permissions ?? self::DEFAULT_FILE_PERMISIONS;
        }
        $this->permissions = $permissions;
        return $this;
    }
}
