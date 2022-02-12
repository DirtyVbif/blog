<?php

namespace Blog\Modules\Library;

abstract class AbstractLibrary
{
    protected const JS_STRICT_MODE = '"use strict";';
    
    abstract public function use(): void;
    abstract protected function getSrcListByKey(string $source_key): array;

    /**
     * Get relative path to current library directory with trailing slash.
     * 
     * @return string `relative/path/to/library/`
     */
    protected function getSelfDir(): string
    {
        $dir = explode('\\', get_called_class());
        unset($dir[0]);
        return LIBDIR . implode('/', $dir) . '/';
    }

    protected function getSrcContent(string $source_key): string
    {
        if (!isset($this->src_content[$source_key])) {
            $this->src_content[$source_key] = '';
            foreach ($this->getSrcListByKey($source_key) as $source) {
                $this->src_content[$source_key] .= file_get_contents($this->getSelfDir() . "{$source_key}/{$source}");
            }
        }
        return $source_key === 'js' ?
            (string)self::JS_STRICT_MODE . $this->src_content[$source_key]
            : $this->src_content[$source_key];
    }
}
