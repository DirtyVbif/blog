<?php

namespace Blog\Database\Components;

trait SQLDataBinding
{
    protected array $data = [];

    protected function setBindValue(array $value_data): string|array
    {
        $field_key = array_keys($value_data)[0];
        $value = $value_data[$field_key];
        if (is_array($value)) {
            // case when array of values given for IN(...) condition
            return $value;
        } else if (is_null($value)) {
            // case when need to insert NULL value into column
            return 'NULL';
        }
        $bind_key = $this->parseBindKeyName($field_key);
        if (isset($this->data[$bind_key])) {
            $i = 1;
            $bind_key = $bind_key . $i;
            while (isset($this->data[$bind_key])) {
                $bind_key = $bind_key . ++$i;
            }
        }
        $this->data[$bind_key] = $value;
        return ":{$bind_key}";
    }

    protected function parseBindKeyName(string $key): string
    {
        return preg_replace(
            ['/\./', '/[\`\s]+/'],
            ['_', ''],
            $key
        );
    }

    public function data(): array
    {
        return $this->data;
    }
}
