<?php

namespace Blog\Database;

class SQLInsert extends SQLAbstractStatement
{
    use Components\SQLDataBinding;

    protected string $table;
    protected array $columns;
    protected array $values;

    /**
     * Specify table name for `SQL INSERT STATEMENT`
     */
    public function into(string $table): self
    {
        $this->table = $this->clearTableName($table);
        return $this;
    }

    /**
     * Specify columns and it's values to insert
     * 
     * @param array $values array of `[value_1, value_2, ...]` values to insert.
     * * Count of values must be equal to count of specified columns
     * * Can also contains arrays with `[value_1, value_2, ...]` values for multiple rows insert e.g.:
     * ```
     * [
     *  [value_1, value_2, ...],
     *  [value_3, value_4, ...]
     * ]
     * ```
     * 
     * @param array|null $columns [optional] specify columns names for `SQL INSERT STATEMENT`.
     * Array must contains only columns names e.g.:
     * `[column_1, column_2, ...]`
     */
    public function set(array $values, ?array $columns = null): self
    {
        if (!is_null($columns)) {
            $this->setColumns($columns);
        }
        $values_flat_array = true;
        foreach ($values as $value) {
            if (is_array($value)) {
                $this->setValues($value);
                $values_flat_array = false;
                continue;
            }
        }
        if ($values_flat_array) {
            $this->setValues($values);
        }
        return $this;
    }

    /**
     * Specify columns names in array
     * 
     * @param array $columns must contains only list of columns names e.g.: `[column_1, column_2, ...]`
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
        return;
    }

    /**
     * Specify values for insert. Count of values must be equal to counts of specified columns
     */
    public function setValues(array $values): void
    {
        if (!isset($this->values) || !is_array($this->values)) {
            $this->values = [];
        }
        $this->values[] = $values;
        return;
    }

    /**
     * @return int last inserted id
     */
    public function exe(): int
    {
        return sql()->insert($this->raw(), $this->data());
    }

    /**
     * {@inheritDoc}
     */
    public function currentSqlString(): string
    {
        $insert_string = "INSERT INTO `{$this->table}`\n\t(%s)\nVALUES";
        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = $this->normalizeColumnName($column);
        }
        $values_stack = [];
        foreach ($this->values as $stack) {
            $values = [];
            for ($i = 0; $i < count($stack); $i++) {
                $column = $this->columns[$i];
                $value = $this->setBindValue([$column => $stack[$i]]);
                $values[] = $value;
            }
            $values_stack[] = "\n\t(" . implode(', ', $values) . ')';
        }
        $insert_string = sprintf($insert_string, implode(', ', $columns));
        $insert_string .= implode(',', $values_stack) . ';';
        return $insert_string;
    }
}