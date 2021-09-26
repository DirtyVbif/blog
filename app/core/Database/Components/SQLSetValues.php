<?php

namespace Blog\Database\Components;

trait SQLSetValues
{
    protected array $set;

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
            $column = $this->normalizeColumnName($column);
            $value = $this->setBindValue([$column => $value]);
            $this->set[$column] = $value;
        }
        return $this;
    }
}