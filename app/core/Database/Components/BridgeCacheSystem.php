<?php

namespace Blog\Database\Components;

use Blog\Modules\Cache\CacheEntity;

trait BridgeCacheSystem
{
    protected CacheEntity $cache;
    /**
     * private table names that must be excluded from cache.
     */
    protected array $PRT = [
        'users_sessions'
    ];

    /**
     * Request type patterns (RTP).
     */
    protected array $RTP = [
        'select' => '/\bselect\s+.*\s+from\s+/i',
        'delete' => '/\bdelete\s+from\s+/i',
        'insert' => '/\binsert\s+into\s+/i',
        'update' => '/\bupdate\s+.*\s+set\s+/i'
    ];

    protected array $parsed_tables = [];

    public function cache(): CacheEntity
    {
        if (!isset($this->cache)) {
            $this->cache = new CacheEntity('sql');
        }
        return $this->cache;
    }

    public function bindVariables(string $raw_request, array $data): string
    {
        $replace_values = [];
        $replace_keys = [];
        $i = 0;
        foreach ($data as $key => $value) {
            // if (!preg_match('/^\d+[\d\.\,]*$/', $value)) {
            if (!is_numeric($value)) {
                $value = "'$value'";
            }
            $replace_values[$i] = $value;
            $replace_keys[$i] = '/\:' . quotemeta($key) . '\b/';
            $i++;
        }
        $request = preg_replace($replace_keys, $replace_values, $raw_request);
        $request = strrws($request);
        $this->markupCacheToUpdate($request);
        return $request;
    }

    protected function getCacheQuery(string $request, bool $all = true): ?array
    {
        if ($this->isPrivateRequest($request)) {
            return null;
        } elseif (!$this->cache()->status()) {
            return null;
        }
        $query = $this->cache()->get($request);
        return $all ? $query : $query[0] ?? null;
    }

    protected function setCacheQuery(string $request, $query)
    {
        if (!$this->isPrivateRequest($request) && !empty($query) && $this->cache()->status()) {
            $this->cache()->set(
                $request,
                $query,
                $this->getTblNamesFromSelect($request)
            );
        }
        return $query;
    }

    /**
     * Check if current SQL request string is cachable
     */
    protected function isPrivateRequest(?string $request = null, ?array $tables = null): bool
    {
        if (empty($tables) && $request) {
            $tables = $this->getTblNamesFromRequest($request);
        }
        foreach ($tables ?? [] as $table) {
            if (in_array($table, $this->PRT)) {
                $private = true;
            }
        }
        return $private ?? false;
    }

    /**
     * Check if current SQL request string makes changes into cache
     */
    protected function markupCacheToUpdate(string $request): void
    {
        // get table names from current SQL-request string
        $tables = $this->getTblNamesFromRequest($request);
        // if current request is private there is no cache
        if ($this->isPrivateRequest(tables: $tables)) {
            return;
        }
        // check if current SQL request string is INSERT / UPDATE / DELETE
        foreach ($this->RTP as $key => $pattern) {
            if (preg_match($pattern, $request) && in_array($key, ['insert', 'update', 'delete'])) {
                $update = true;
                break;
            }
        }
        // if update required
        if ($update ?? false) {
            $this->cache()->markupToUpdate($tables);
        }
        return;
    }

    protected function getTblNamesFromRequest(string $request): array
    {
        if ($names = $this->parsed_tables[$request] ?? false) {
            return $names;
        }
        switch ($this->getRequestType($request)) {
            case 'select':
                $this->parsed_tables[$request] = $this->getTblNamesFromSelect($request);
                break;
            case 'update':
                $this->parsed_tables[$request] = $this->getTblNamesFromUpdate($request);
                break;
            case 'insert':
                $this->parsed_tables[$request] = $this->getTblNamesFromInsert($request);
                break;
            case 'delete':
                $this->parsed_tables[$request] = $this->getTblNamesFromDelete($request);
                break;
            default:
                pre(
                    'Can\'t recognize sql request type from SQL request string:'
                        . '<br><pre>' . $request . '</pre>'
                );
        }
        if (empty($this->parsed_tables[$request] ?? null)) {
            pre(
                'Can\'t get table names from SQL request string:'
                    . '<br><pre>' . $request . '</pre>'
            );
        }
        return $this->parsed_tables[$request];
    }

    protected function getRequestType(string $request): ?string
    {
        foreach ($this->RTP as $type => $pattern) {
            if (preg_match($pattern, $request)) {
                return $type;
            }
        }
        return null;
    }

    protected function getTblNamesFromSelect(string $request): array
    {
        // remove all backqoutes from request string
        $request = str_replace('`', '', $request);
        // trim string part of request with table names
        $names = preg_replace(
            [
                '/\s*(\bleft\b\s*)?(\bright\b\s*)?(\bfull\b\s*)?(\bouter\b\s*)?(\binner\b\s*)?\bjoin\b\s*/i',
                '/\s*\bon\s+(\b\w+\.)?\w+\b\s+=\s(\b\w+\.)?\w+\s*/i',
                '/\s*\busing\b\(\s*\b\w+\b\s*\)\s*/i',
                '/\s*\bselect\b[\s\w\.\*\,]+\bfrom\s+/i',
                '/\s*\bwhere\s.*/i',
                '/\s*\border\s+by\b.*/i',
                '/\s*\bgroup\s+by\b.*/i',
                '/;/'
            ],
            [', ', ' ', ' ', '', '', '', ''],
            $request
        );
        // remove table aliases name
        $names = preg_replace('/(\b\w+\.)?(\b\w+\b)((\s+as)?\s+\w+)?(\s*\,\s*)?/i', '$2,', $names);
        $names = $this->splitTblNames($names);
        return $names;
    }

    protected function getTblNamesFromUpdate(string $request): array
    {
        // parse request string into array with affected tables names
        $request = str_replace('`', '', $request);
        $pattern = '/(^.*\bupdate\b\s*)(\b\w+\b\s*\.\s*)?(\b\w+\b)(\s*(\bas\s+)?\w+)?(\s+set\b.*;?$)/i';
        $names = preg_replace($pattern, '$3, ', $request);
        $names = $this->splitTblNames($names);
        return $names;
    }

    protected function getTblNamesFromInsert(string $request): array
    {
        // parse request string into array with affected tables names        
        $request = str_replace('`', '', $request);
        $pattern = '/(^.*\binsert\s+into\b\s*)(\b\w+\b\s*\.\s*)?(\b\w+\b)(\s*(\bas\s+)?\w+)?(\s*\([\w\,\s\.]+\)\s*)?(\s*\bvalues\b.*;?$)/i';
        $names = preg_replace($pattern, '$3, ', $request);
        $names = $this->splitTblNames($names);
        return $names;
    }

    protected function getTblNamesFromDelete(string $request): array
    {
        // parse request string into array with affected tables names
        $request = str_replace('`', '', $request);
        $pattern = '/(^.*\bdelete\s+from\b\s*)(\b\w+\b\s*\.\s*)?(\b\w+\b)(\s*(\bas\b\s*)?\w+)?((\s*\bwhere\b).*;?$)/i';
        $names = preg_replace($pattern, '$3, ', $request);
        $names = $this->splitTblNames($names);
        return $names;
    }

    protected function splitTblNames(string $names, string $pattern = '/[\s\,]+/'): array
    {
        $names = preg_split($pattern, $names);
        foreach ($names as $i => $name) {
            if (!$name) {
                unset($names[$i]);
            }
        }
        return $names;
    }
}
