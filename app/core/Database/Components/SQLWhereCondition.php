<?php

namespace Blog\Database\Components;

trait SQLWhereCondition
{
    use SQLDataBinding;
    
    protected array $where_conditions = [];

    /**
     * Add first `WHERE` condition
     * 
     * @param array $condition must contains:
     * * only one pair of `columns_name => value` condition
     * * or `columns_name => NULL` if using operator `NULL` or `NOT NULL`
     * * or `NULL` if using @param $equal_columns
     * @param bool $not if true will use `WHERE NOT` clause
     */
    public function where(
        ?array $condition = null,
        string $operator = '=',
        ?array $equal_columns = null,
        bool $not = false
    ): self {
        return $this->whereAdd(
            condition: $condition,
            operator: $operator,
            equal_columns: $equal_columns,
            not: $not
        );
    }

    /**
     * Add one more `AND WHERE` condition after previous `WHERE` condition
     * 
     * @param array $condition must contains:
     * * only one pair of `columns_name => value` condition
     * * or `columns_name => NULL` if using operator `NULL` or `NOT NULL`
     * * or `NULL` if using @param $equal_columns
     * @param bool $not if true will use `AND NOT` clause
     */
    public function andWhere(
        ?array $condition = null,
        string $operator = '=',
        ?array $equal_columns = null,
        bool $not = false
    ): self {
        $this->whereAdd(
            condition: $condition,
            operator: $operator,
            equal_columns: $equal_columns,
            not: $not,
            type: 'AND');
        return $this;
    }

    /**
     * Add one more `OR WHERE` condition after previous `WHERE` condition
     * 
     * @param array $condition must contains:
     * * only one pair of `columns_name => value` condition
     * * or `columns_name => NULL` if using operator `NULL` or `NOT NULL`
     * * or `NULL` if using @param $equal_columns
     * @param bool $not if true will use `OR NOT` clause
     */
    public function orWhere(
        ?array $condition = null,
        string $operator = '=',
        ?array $equal_columns = null,
        bool $not = false
    ): self {
        $this->whereAdd(
            condition: $condition,
            operator: $operator,
            equal_columns: $equal_columns,
            not: $not,
            type: 'OR');
        return $this;
    }

    protected function whereAdd(
        ?array $condition,
        string $operator,
        ?array $equal_columns,
        bool $not,
        ?string $type = null
    ): self {
        if ($condition) {
            $condition = $this->parseWhereCondition($condition);
        } else if ($equal_columns) {
            $condition = $this->parseWhereConditionPair($equal_columns);
        } else {
            return $this;
        }
        $condition['op'] = $operator;
        $condition['not'] = $not;
        if (!isset($this->where_conditions[0])) {
            $this->where_conditions[0] = $condition;
        } else {
            $condition['type'] = $type;
            array_push($this->where_conditions, $condition);
        }
        return $this;
    }

    protected function parseWhereCondition(array $condition): array
    {
        $column = array_keys($condition)[0];
        if (is_null($condition[$column])) {
            $bind_value = null;
        } else {
            $bind_value = $this->setBindValue($condition);
        }
        return $this->parseWhereConditionPair([$column, $bind_value]);
    }

    protected function parseWhereConditionPair(array $pair): array
    {
        $condition = $this->normalizeColumnName($pair[0]);
        $equal = is_string($pair[1]) ? $this->normalizeColumnName($pair[1]) : $pair[1];
        return [
            'cond' => [
                $condition, $equal
            ]
        ];
    }

    protected function parseWhereBindKey(string $key): string
    {
        return preg_replace(
            ['/\./', '/\`/'],
            ['_', ''],
            $key
        );
    }

    protected function currentSqlStringWhereCondition(): string
    {
        if (empty($this->where_conditions)) {
            return '';
        }
        $condition_string = "\nWHERE ";
        foreach ($this->where_conditions as $where) {
            $prefix = isset($where['type']) ? " {$where['type']} " : '';
            $prefix .= $where['not'] ? 'NOT ' : '';
            $value = is_null($where['cond'][1]) ? '' : $where['cond'][1];
            switch (true) {
                case preg_match('/^in$/i', $where['op']):
                    $values = is_array($value) ? implode(', ', $value) : $value;
                    $where['op'] = sprintf('IN(%s)', $values);
                    break;
                default: $where['op'] .= $value ? ' ' . $value : '';
            }
            $condition_string .= $prefix . $where['cond'][0] . ' ' . $where['op'];
        }
        return $condition_string;
    }
}
