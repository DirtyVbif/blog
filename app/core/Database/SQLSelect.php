<?php

namespace Blog\Database;

class SQLSelect extends SQLAbstractStatement
{
    use Components\SQLWhereCondition;
    use Components\SQLDataBinding;

    protected array $columns;
    protected array $from;
    protected array $order;
    protected array $join = [];
    protected array $first_table_columns;
    protected int $limit;
    protected int $limit_offset;
    protected $result;

    /**
     * Specify table name or names for SQL SELECT FROM query
     * 
     * @param string|array $tables must contains:
     * * name of only one table if string
     * * pairs of table_alias and table_name in array e.g.: `['alias_1' => 'table_1', 'alias_2' => 'table_2', ...]`
     * * or only table_names in array e.g.: `['table_1', 'table_2', ...]`
     */
    public function from(string|array $tables): self
    {
        if (is_string($tables)) {
            $tables = [$tables];
        }
        foreach ($tables as $alias => $table) {
            $table = $this->clearTableName($table);
            $this->from[$table] = [
                'name' => $table,
                'as' => is_numeric($alias) ? null : (string)$alias
            ];
        }
        if (isset($this->first_table_columns)) {
            $table = array_keys($this->from)[0];
            $table = $this->from[$table];
            $this->columns($this->first_table_columns, $table['as'] ?? $table['name']);
            unset($this->first_table_columns);
        }
        return $this;
    }

    /**
     * Specify selection set of columns for concrete table
     * 
     * @param array $columns must contains:
     * * pairs of column_alias and column_name in array e.g.: `['alias_1' => 'column_1', 'alias_2' => 'column_2', ...]`
     * * array pairs of columns_alias and column_name with table_name or table_alias as array key e.g.: `['table_1' => ['alias_1' => 'column_1', ...], 'table_2' => [...]]`
     * * or only column_names in array e.g.: `['column_1', 'column_2', ...]`
     * @param string|null $from_table [optional] table name or table name alias of table wich contains specified columns
     * or null to use first table name from tables list. Using first table name by default
     */
    public function columns(array $columns, ?string $from_table = null): self
    {
        if (!is_null($from_table)) {
            $from_table = $this->clearTableName($from_table, false);
        }
        $first_table_columns = [];
        foreach ($columns as $alias => $name) {
            if (is_array($name) && !is_numeric($alias)) {
                $this->columns($name, $alias);
                continue;
            } else if (is_null($from_table)) {
                if (!isset($this->from)) {
                    $first_table_columns[$alias] = $name;
                    continue;
                }
                $table = array_keys($this->from)[0];
                $table = $this->from[$table];
                $from_table = $table['as'] ?? $table['name'];
            }
            $this->columns[$from_table][$name] = [
                'name' => $this->clearColumnName($name),
                'as' => is_numeric($alias) ? null : $alias
            ];
        }
        if (!empty($first_table_columns)) {
            $this->first_table_columns = $first_table_columns;
        }
        return $this;
    }

    /**
     * Add `SQL JOIN` into selection query
     * 
     * @param array $table table name for join must contains:
     * * only one pair of `table_alias => table_name`
     * * or only one value `table_name`
     * @param array|null $on if using `JOIN ON` condition type set array with two columns name that would use @param $on_operator as operator. Table name prefix for column names are available.
     * `array $on` must contains only 2 values with column names that must be equals with array keys `0` and `1` e.g.:
     * `[0 => 'column_name_1', 1 => 'column_name_2']`
     * @param array|null $using if using `JOIN USING()` condition type set name of using column. Table name prefix for column names are available
     * @param string $type any available `SQL JOIN` type
     * @param string $on_operator any available `SQL cONDITION OPERATOR` for `JOIN ON` condition only
     */
    public function join(
        array $table,
        ?array $on = null,
        ?string $using = null,
        string $type = 'LEFT',
        string $on_operator = '='
    ): self {
        $as = array_keys($table)[0];
        $table_name = $table[$as];
        if (!is_null($on)) {
            $on = [
                0 => $this->normalizeColumnName($on[0]),
                1 => $this->normalizeColumnName($on[1]),
                'op' => $on_operator
            ];
        }
        $this->join[$table_name] = [
            'table' => $table_name,
            'as' => is_numeric($as) ? null : $as,
            'on' => $on,
            'using' => $using,
            'type' => $type
        ];
        return $this;
    }

    public function order(array|string $column, string $order = 'ASC'): self
    {
        if (is_array($column)) {
            foreach ($column as $c) {
                $this->orderAdd($c, $order);
            }
        } else {
            $this->orderAdd($column, $order);
        }
        return $this;
    }

    protected function orderAdd(string $column, string $order): void
    {
        $column = $this->normalizeColumnName($column);
        if (!isset($this->order)) {
            $this->order = [];
        }
        array_push($this->order, [
            'by' => $column,
            'order' => $order
        ]);
        return;
    }

    /**
     * {@inheritDoc}
     * 
     * First given argument must be @var bool that means to select only first matched row if `TRUE` or all matched rows if `FALSE`.
     * 
     * @param bool $first to select only first but not all matched rows if `TRUE` passed as first argument. `FALSE` by default and all matches will be selected.
     */
    public function exe(): array
    {
        $args = func_get_args();
        if (isset($first)) {
            unset($args[0]);
        } else {
            $first = $args[0] ?? false;
        }
        $this->current_sql_string = $this->currentSqlString();
        if (!isset($this->result) || !$this->compareWithPreviousRequest()) {
            $this->previous_sql_string = $this->current_sql_string;
            $this->result = $first ?            
                sql()->selectFirst($this->current_sql_string, $this->data())
                : sql()->select($this->current_sql_string, $this->data());
        }
        return $this->result;
    }

    /**
     * Select all matched rows
     */
    public function all(): array
    {
        return $this->exe(false);
    }

    /**
     * Select only first match row
     */
    public function first(): array
    {
        return $this->exe(true);
    }

    /**
     * Count total rows in table
     */
    public function count(): int
    {
        $sql_string = sprintf(
            'SELECT COUNT(*) as count FROM %s;',
            $this->currentSqlStringTables()
        );
        $result = sql()->query($sql_string)->fetch();
        return (int)$result['count'];
    }

    /**
     * {@inheritDoc}
     */
    public function currentSqlString(): string
    {
        $sql_string = "SELECT\n\t%s\nFROM %s";
        $columns = $this->currentSqlStringColumns();
        $tables = $this->currentSqlStringTables();
        $sql_string = sprintf($sql_string, $columns, $tables);
        $where_condition = $this->currentSqlStringWhereCondition();
        $join = $this->currentSqlStringJoin();
        $order = $this->currentSqlStringOrder();
        $limit = $this->currentSqlStringLimit();
        $sql_string .= $join . $where_condition . $order . $limit . ';';
        return $sql_string;
    }

    protected function currentSqlStringColumns(): string
    {
        if (!isset($this->columns)) {
            return '*';
        }
        $columns_to_select = [];
        foreach ($this->columns as $t => $columns) {
            foreach ($columns as $column) {
                $as = $column['as'] ? " AS {$this->normalizeColumnName($column['as'])}" : '';
                $col2sel = $this->normalizeColumnName("{$t}.{$column['name']}") . $as;
                array_push($columns_to_select, $col2sel);
            }
        }
        return implode(', ', $columns_to_select);
    }

    protected function currentSqlStringTables(): string
    {
        $from_tables = [];
        foreach ($this->from as $table) {
            array_push(
                $from_tables,
                $this->normalizeTableName($table['name'], $table['as'])
            );
        }
        return implode(', ', $from_tables);
    }

    protected function currentSqlStringJoin(): string
    {
        $join_string = '';
        $q = $this->q;
        if (!empty($this->join)) {
            foreach ($this->join as $join) {
                $join_type = strSuffix($join['type'], ' JOIN');
                if ($join['using']) {
                    $join_condition = " USING({$q}{$join['using']}{$q})";
                } else if ($join['on']) {
                    $on = "{$join['on'][0]} {$join['on']['op']} {$join['on'][1]}";
                    $join_condition = sprintf(' ON %s', $on);
                }
                $join_string_add = $join_type . ' '
                    . $this->normalizeTableName($join['table'], $join['as'])
                    . $join_condition;
                $join_string .= "\n" . preg_replace('/\s+/', ' ', $join_string_add);
            }
        }
        return $join_string;
    }

    protected function currentSqlStringOrder(): string
    {
        $order_string = '';
        if (isset($this->order) && !empty($this->order)) {
            $order_string .= ' ORDER BY ';
            $columns = [];
            foreach ($this->order as $o) {
                array_push($columns, "{$o['by']} {$o['order']}");
            }
            $order_string .= implode(', ', $columns);
        }
        return $order_string;
    }

    protected function currentSqlStringLimit(): string
    {
        $limit = '';
        if ($this->limit()) {
            $limit .= "\nLIMIT " . $this->limit();
            $limit .= $this->limitOffset() ? ' OFFSET ' . $this->limitOffset() : '';
        }
        return $limit;
    }

    public function limit(?int $limit = null): int|self|null
    {
        if (is_null($limit)) {
            return $this->limit ?? 0;
        }
        $this->limit = max($limit, 0);
        return $this;
    }

    public function limitOffset(?int $offset = null): int|self|null
    {
        if (is_null($offset)) {
            return $this->limit_offset ?? 0;
        }
        $this->limit_offset = max($offset, 0);
        return $this;
    }
}
