<?php

namespace Blog\Database\Components;

trait Helpers
{
    protected string $driver;
    protected string $q = '';
    protected string $schema;

    public function __construct()
    {
        $this->driver = app()->env()->DB['DRIVER'];
        $this->schema = app()->env()->DB['SCHEMA'] ?? null;
        switch ($this->driver) {
            case 'mysql':
                $this->q = '`';
                break;
        }
    }

    protected function clearTableName(string $table_name): string
    {
        return preg_replace('/(`?\w+`?\.)?(`)?(\w+)(`)?(.*)/', '$3', $table_name);
    }

    protected function clearColumnName(string $column_name): string
    {
        return $this->clearTableName($column_name);
    }

    protected function normalizeColumnName(string $column): string
    {
        $pattern = '/((`)?(\w+)(`)?(\.))?(`)?(\w+)(`)?/';
        $skip_pattern = '/\:\w+/';
        $q = $this->q;
        if (preg_match($pattern, $column) && !preg_match($skip_pattern, $column)) {
            $column = str_replace('`', '', $column);
            $parts = explode('.', $column);
            foreach ($parts as &$part) {
                $part = "{$q}{$part}{$q}";
            }
            $column = implode('.', $parts);
        }
        return $column;
    }

    protected function normalizeTableName(string $table_name, ?string $alias_name = null): string
    {
        $q = $this->q;
        $table_name = $this->clearTableName($table_name);
        if ($this->driver === 'pgsql') {
            $table_name = app()->env()->DB['SCHEMA'] . '.' . $table_name;
        }
        $table_alias = $alias_name ? $this->clearTableName($alias_name) : null;
        $as = $table_alias ? " AS {$q}{$table_alias}{$q}" : '';
        return "{$q}{$table_name}{$q}{$as}";
    }

    protected function isMysql(): bool
    {
        return $this->driver === 'mysql';
    }

    protected function isPgsql(): bool
    {
        return $this->driver === 'pgsql';
    }
}