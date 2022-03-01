<?php

namespace Blog\Database;

abstract class SQLAbstractStatement
{
    protected $result;
    protected string $current_sql_string;
    protected string $previous_sql_string;
    protected string $driver;
    protected string $q = '';

    public function __construct()
    {
        $this->driver = app()->env()->DB['DRIVER'];
        switch ($this->driver) {
            case 'mysql':
                $this->q = '`';
                break;
        }
    }

    /**
     * Base method to execute currently prepared `SQL REQUEST`
     * 
     * @return mixed SQL QUERY RESULT
     */
    abstract public function exe();

    /**
     * Get currently prepared `SQL REQUEST STRING`. Also has public alias @method raw()
     */
    abstract public function currentSqlString(): string;

    /**
     * This method is an alias and equivalent to @method currentSqlString()
     */
    public function raw(): string
    {
        return $this->currentSqlString();
    }
    
    protected function compareWithPreviousRequest(): bool
    {
        $this->current_sql_string = $this->currentSqlString();
        if (!isset($this->previous_sql_string)) {
            return false;
        }
        return hash_equals(
            md5($this->current_sql_string),
            md5($this->previous_sql_string)
        );
    }

    protected function clearTableName(string $table_name): string
    {
        return preg_replace('/(`?\w+`?\.)?(`)?(\w+)(`)?(.*)/', '$3', $table_name);
    }

    protected function clearColumnName(string $column_name): string
    {
        return $this->clearTableName($column_name);
    }

    protected function normalizeColumnName(?string $column): string
    {
        $pattern = '/((`)?(\w+)(`)?(\.))?(`)?(\w+)(`)?/';
        $skip_pattern = '/\:\w+/';
        $q = $this->q;
        if (!is_null($column) && preg_match($pattern, $column) && !preg_match($skip_pattern, $column)) {
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
}
