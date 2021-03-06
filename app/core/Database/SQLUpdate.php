<?php

namespace Blog\Database;

class SQLUpdate extends SQLAbstractStatement
{
    use Components\SQLWhereCondition,
        Components\SQLDataBinding;

    protected string $table;
    protected array $set;
    protected string $statement = 'update';

    /**
     * Specify table name for update
     */
    public function table(string $name): self
    {
        $this->table = $this->clearTableName($name);
        return $this;    
    }

    public function getRequestTables(): array
    {
        $tables = [];
        if (isset($this->table)) {
            $tables[] = $this->table;
        }
        return $tables;
    }

    /**
     * Specify columns and it's values to update/insert
     * 
     * @param array $values array of `column_name => column_value` to update/insert. Each array key must be equal to column name and has value to update/insert.
     */
    public function set(array $values): self
    {
        foreach ($values as $column => $value) {
            if (!is_string($column)) {
                continue;
            }
            $column = $this->clearColumnName($column);
            $value = $this->setBindValue([$column => $value]);
            $this->set[$column] = $value;
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     * Execute current `SQL STATEMENT REQUEST`
     * 
     * To change current statement @see @method setUpd() or @method setDel()
     * 
     * @return int number of changes in table
     */
    public function exe(): int
    {
        return sql()->change($this, $this->data());
    }

    /**
     * Execute `SQL UPDATE` request
     * 
     * @return int number of affected rows in table
     */
    public function update(): int
    {
        $this->setUpd();
        return $this->exe();
    }

    /**
     * Execute `SQL DELETE` request
     * 
     * @return int number of deleted rows
     */
    public function delete(): int
    {
        $this->setDel();
        return $this->exe();
    }

    /**
     * {@inheritDoc}
     */
    public function currentSqlString(bool $bind = false): string
    {
        $sql_string = '';
        if ($this->isUpdate()) {
            $sql_string .= 'UPDATE ' . $this->normalizeTableName($this->table) . "\nSET";
            $sql_string .= $this->currentSqlStringSet();
        } else if ($this->isDelete()) {
            $sql_string .= 'DELETE FROM ' . $this->normalizeTableName($this->table);
        }
        $sql_string .= $this->currentSqlStringWhereCondition() . ';';
        return $bind ? $this->bind($sql_string, $this->data()) : $sql_string;
    }

    protected function currentSqlStringSet(): string
    {
        $set = [];
        foreach ($this->set as $column => $value) {
            if ($function = $this->column_functions[$column] ?? false) {
                $value = "{$function}({$value})";
            }
            $set[] = $this->normalizeColumnName($column) . " = {$value}";
        }
        return "\n\t" . implode(', ', $set);
    }

    protected function isUpdate(): bool
    {
        return $this->statement === 'update';
    }

    /**
     * Change current `SQL STATEMENT` to `SQL UPDATE STATEMENT`
     */
    public function setUpd(): self
    {
        $this->statement = 'update';
        return $this;
    }

    protected function isDelete(): bool
    {
        return $this->statement === 'delete';
    }

    /**
     * Change current `SQL STATEMENT` to `SQL DELETE STATEMENT`
     */
    public function setDel(): self
    {
        $this->statement = 'delete';
        return $this;
    }

    public function useFunction(string $column, string $function, ?string $column_alias = null): self
    {
        $this->column_functions[$column] = strtoupper($function);
        return $this;
    }
}
