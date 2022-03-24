<?php

namespace Blog\Modules\Library;

abstract class AbstractLibrary
{
    protected const JS_STRICT_MODE = '"use strict";';
    protected array $src_content = [];
    
    abstract public function use(): void;

    /**
     * Method to get data of using js and css sources for library as object with `stack` and `public` fields.
     * 
     * @return object must contains `stack` and `public` fields, where:
     * * `stack` is @var string[] with names of sources;
     * * `public` is @var string name of public source file.
     */
    abstract protected function getSources(): object;

    /**
     * Get absolute path to current library directory with trailing slash `/`.
     * 
     * @return string `%ROOT%/path/to/library/`
     */
    protected function getSelfDir(): string
    {
        return LIBDIR . parseClassname(static::class)->classname . '/';
    }

    /**
     * Get absolute path to current library source directory with trailing slash `/`.
     * 
     * @return string `%ROOT%/path/to/library/src/`
     */
    protected function getSelfSrcDir(): string
    {
        return $this->getSelfDir() . 'src/';
    }

    protected function getSrcContent(string $source_type): string
    {
        if (!isset($this->src_content[$source_type])) {
            $this->src_content[$source_type] = '';
            foreach ($this->getSources()?->{$source_type}['stack'] ?? [] as $source) {
                $filename = $this->getSelfDir() . strPrefix($source, '/', true);
                $this->src_content[$source_type] .= file_get_contents($filename);
            }
        }
        return $this->src_content[$source_type];
    }

    protected function getJsSrcContent(): string
    {
        return (string)self::JS_STRICT_MODE . $this->getSrcContent('js');
    }

    protected function getCssSrcContent(): string
    {
        return $this->getSrcContent('css');
    }

    protected function checkPublicSources(): void
    {
        if (isset($this->getSources()?->js) && !$this->verifyPublicSource('js')) {
            $this->makeSourcePublic('js');
        }
        if (isset($this->getSources()?->css) && !$this->verifyPublicSource('css')) {
            $this->makeSourcePublic('css');
        }
        return;
    }

    protected function verifyPublicSource(string $source_type): bool
    {
        $public_filename = $this->getSources()?->{$source_type}['public'];
        $public_filename = PUBDIR . strPrefix($public_filename, '/');
        $public_file = f($public_filename);
        if (!$public_file->exists()) {
            return false;
        }
        $get_method = camelCase("get {$source_type} src content");
        return hash_equals(
            md5($this->$get_method()),
            md5($public_file->realContent())
        );
    }

    protected function makeSourcePublic(string $source_type): void
    {
        $get_method = camelCase("get {$source_type} src content");
        if ($public_name = $this->getSources()?->{$source_type}['public'] ?? null) {
            $public_name = PUBDIR . strPrefix($public_name, '/');
            $public_file = f($public_name);
            $public_file->content($this->{$get_method}());
            $public_file->save();
        }
        return;
    }
}
