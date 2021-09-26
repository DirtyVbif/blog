<?php

namespace Blog\Database;

abstract class SQLAbstractStatement
{
    protected $result;
    protected string $current_sql_string;
    protected string $previous_sql_string;

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
        if (!is_null($column) && preg_match($pattern, $column) && !preg_match($skip_pattern, $column)) {
            $column = str_replace('`', '', $column);
            $parts = explode('.', $column);
            foreach ($parts as &$part) {
                $part = "`{$part}`";
            }
            $column = implode('.', $parts);
        }
        return $column;
    }
}
