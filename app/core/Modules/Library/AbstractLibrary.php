<?php

namespace Blog\Modules\Library;

abstract class AbstractLibrary
{
    protected const JS_STRICT_MODE = '"use strict";';
    
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
     * Get relative path to current library directory with trailing slash `/`.
     * 
     * @return string `relative/path/to/library/`
     */
    protected function getSelfDir(): string
    {
        $dir = explode('\\', get_called_class());
        unset($dir[0]);
        return LIBDIR . implode('/', $dir) . '/';
    }

    protected function getJsSrcContent(): string
    {
        if (!isset($this->src_content['js']) && isset($this->getSources()->js)) {
            $this->src_content['js'] = '';
            foreach ($this->getSources()->js['stack'] as $source) {
                $filename = $this->getSelfDir() . strPrefix($source, '/', true);
                $this->src_content['js'] .= file_get_contents($filename);
            }
        }
        return (string)self::JS_STRICT_MODE . $this->src_content['js'];
    }

    protected function checkPublicSources(): void
    {
        if (isset($this->getSources()->js) && !$this->verifyPublicJsSource()) {
            $this->makePublicJsSource();
        }
        if (isset($this->getSources()->css) && !$this->verifyPublicCssSource()) {
            $this->makePublicCssSource();
        }
        return;
    }

    protected function verifyPublicJsSource(): bool
    {
        $public_filename = $this->getSources()->js['public'];
        $public_file = f($public_filename);
        if (!$public_file->exists()) {
            return false;
        }
        return hash_equals(
            md5($this->getJsSrcContent()),
            md5($public_file->realContent())
        );
    }

    protected function makePublicJsSource(): void
    {
        if ($public_name = $this->getSources()?->js['public'] ?? null) {
            f($public_name)
                ->content($this->getJsSrcContent())
                ->save();
        }
        return;
    }

    protected function verifyPublicCssSource(): bool
    {
        // TODO: complete verification of library public css
        return false;
    }

    protected function makePublicCssSource(): void
    {
        return;
    }
}
