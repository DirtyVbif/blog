<?php

namespace Blog\Database;

abstract class SQLAbstractStatement
{
    use Components\Helpers;

    protected $result;
    protected string $current_sql_string;
    protected string $previous_sql_string;
    protected array $column_functions = [];

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

    abstract public function useFunction(string $column_name, string $function, ?string $column_alias): self;

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
}
