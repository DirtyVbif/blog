<?php

namespace Blog\Database;

class SQLTable
{
    use Components\Helpers;

    protected array $data = [];
    protected string $schema;
    protected string $driver;

    public function __construct(
        protected string $name
    ) {
        $this->schema = app()->env()->DB['SCHEMA'];
        $this->driver = app()->env()->DB['DRIVER'];
    }

    public function getPkName(): string
    {
        if (!isset($this->data['pk_name'])) {
            switch ($this->driver) {
                case 'mysql':
                    $sql = 'SELECT COLUMN_NAME AS `name`'
                    . ' FROM INFORMATION_SCHEMA.COLUMNS'
                    . ' WHERE TABLE_SCHEMA = \'' . $this->schema . '\''
                    . ' AND TABLE_NAME = \'' . $this->name . '\''
                    . ' AND COLUMN_KEY = \'PRI\';';
                    break;
                case 'pgsql':
                    $sql = 'SELECT a.attname AS name FROM pg_index i JOIN pg_attribute a'
                    . ' ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey)'
                    . ' WHERE i.indrelid = \'' . $this->schema . '.' . $this->name . '\'::regclass'
                    . ' AND i.indisprimary;';
                    break;
            }
            $result = sql()->query($sql)->fetch();
            $this->data['pk_name'] = $result['name'];
        }
        return $this->data['pk_name'];
    }
}