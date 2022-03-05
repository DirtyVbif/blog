<?php

namespace Blog\Database;

class SQLInsert extends SQLAbstractStatement
{
    use Components\SQLDataBinding;

    protected string $table;
    protected array $columns = [];
    protected array $values = [];

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
     * * Can also contains array of arrays with values for multiple rows insert e.g.:
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
        $this->setValues($values);
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
        $this->values_binded = false;
        $flat_array = false;
        foreach ($values as $value) {
            if (!is_array($value)) {
                $flat_array = true;
                break;
            }
        }
        if ($flat_array) {
            $this->values[] = $values;
        } else {
            foreach ($values as $value) {
                $this->setValues($value);
            }
        }
        return;
    }

    /**
     * @param bool $rows_affected return number of affected rows or last inserted id
     * @return string last inserted id or number of affected rows
     */
    public function exe(bool $rows_affected = false): string|false
    {
        $this->bindValues();
        $last_inserted_id = $this->isPgsql() ?
            sprintf(
                '%s.%s_%s_seq',
                $this->schema,
                $this->table,
                table($this->table)->getPkName()
            ) : null;
        $method = $rows_affected ? 'change' : 'insert';
        return sql()->$method($this->raw(), $this->data(), $last_inserted_id);
    }

    /**
     * {@inheritDoc}
     */
    public function currentSqlString(): string
    {
        $insert_string = 'INSERT INTO ' . $this->normalizeTableName($this->table) . "\n\t(%s)\nVALUES";
        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = $this->normalizeColumnName($column);
        }
        $values_stack = [];
        foreach ($this->values as $stack) {
            $values = [];
            foreach ($stack as $i => $value) {
                $column = $this->columns[$i];
                if ($function = $this->column_functions[$column] ?? false) {
                    $value = "{$function}({$value})";
                }
                $values[] = $value;
            }
            $values_stack[] = "\n\t(" . implode(', ', $values) . ')';
        }
        $insert_string = sprintf($insert_string, implode(', ', $columns));
        $insert_string .= implode(',', $values_stack) . ';';
        return $insert_string;
    }

    protected function bindValues(): void
    {
        if (!$this->values_binded) {
            foreach ($this->values as $i => $values) {
                foreach ($values as $v => $value) {
                    if (preg_match('/^\:[a-z]\w*$/i', $value ?? '')) {
                        continue;
                    }
                    $this->values[$i][$v] = $this->setBindValue([$this->columns[$v] => $value]);
                }
            }
            $this->values_binded = true;
        }
        return;
    }

    public function useFunction(string $column, string $function, ?string $column_alias = null): self
    {
        $this->column_functions[$column] = strtoupper($function);
        return $this;
    }
}