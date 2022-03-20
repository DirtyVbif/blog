<?php

namespace Blog\Database;

abstract class SQLAbstractStatement
{
    use Components\Helpers;

    protected $result;
    protected string $current_sql_string;
    protected string $previous_sql_string;
    protected array $column_functions = [];
    protected bool $skip_cache = false;

    /**
     * Base method to execute currently prepared `SQL REQUEST`
     * 
     * @return mixed SQL QUERY RESULT
     */
    abstract public function exe();

    /**
     * Get currently prepared `SQL REQUEST STRING`. Has an alias @method raw()
     * 
     * @param bool $bind is value binding required or not. You don't need to use it for PDO::query()
     */
    abstract public function currentSqlString(bool $bind = false): string;

    abstract public function useFunction(string $column_name, string $function, ?string $column_alias): self;

    abstract public function getRequestTables(): array;

    abstract public function data(): array;

    /**
     * This method is an alias and to @method currentSqlString()
     * 
     * @param bool $bind @see @method currentSqlString()
     */
    public function raw(bool $bind = false): string
    {
        return $this->currentSqlString($bind);
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

    /**
     * Set request cache status
     */
    public function enableCache(): self
    {
        $this->skip_cache = false;
        return $this;
    }

    /**
     * Set sql statement cache status as disabled
     */
    public function disableCache(): self
    {
        $this->skip_cache = true;
        return $this;
    }

    /**
     * Checks if cache enabled for current SQL STATEMENT
     */
    public function cacheAvailable(): bool
    {
        return !$this->skip_cache;
    }
}
