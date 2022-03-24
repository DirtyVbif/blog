<?php

namespace Blog\Database\Components;

trait SQLDataBinding
{
    protected array $data = [];

    protected function setBindValue(array $value_data): string|array
    {
        $field_key = array_keys($value_data)[0];
        $value = $value_data[$field_key];
        $skip_pattern = '/^({{)([^}]+)(}})$/';
        if (is_array($value)) {
            // case when array of values given for IN(...) condition
            return $value;
        } else if (is_null($value)) {
            // case when need to insert NULL value into column
            return 'NULL';
        } else if (preg_match($skip_pattern, $value)) {
            return preg_replace($skip_pattern, '$2', $value);
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

    public function bind(string $sql_request, array $pdo_data): string
    {
        // check data array for values
        if (empty($pdo_data)) {
            return $sql_request;
        }
        $replace_values = $replace_keys = [];
        foreach ($pdo_data as $key => $value) {
            $replace_values[] = is_numeric($value) ? $value : "'{$value}'";
            $replace_keys[] = '/\:' . strRegexQuote($key) . '/';
        }
        // replace pdo bindable keys with values
        $request_string = preg_replace($replace_keys, $replace_values, $sql_request);
        // remove extra white spaces from request string
        $request_string = strrws($request_string);
        return $request_string;
    }
}
