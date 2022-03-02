<?php

namespace Blog\Database;

use Exception;
use PDO;
use PDOStatement;

class Bridge
{
    use Components\BridgeCacheSystem;
    
    private object $config;

    private PDO $connection;
    private PDOStatement $statement;

    public function __construct()
    {
        $this->config = (object)app()->env()->DB;
        return $this;
    }

    private function cfg(string $key): string
    {
        $key = strtoupper($key);
        return $this->config->$key;
    }
    
    /**
     * Get current PDO::connection to database
     */
    public function connect(): PDO
    {
        if (!isset($this->connection)) {
            $this->connection = $this->establisheConnection();
        }
        return $this->connection;
    }

    private function establisheConnection(): PDO
    {
        $dsn = null;
        $options = (array)app()->config('pdo');
        switch ($this->cfg('driver')) {
            case 'mysql':
                $dsn = $this->cfg('driver')
                    . ':host=' . $this->cfg('host')
                    . ';dbname=' . $this->cfg('name');
                break;
            case 'pgsql':
                $dsn = $this->cfg('driver')
                    . ':host=' . $this->cfg('host')
                    . ';port=' . $this->cfg('port')
                    . ';dbname=' . $this->cfg('name');
                break;
            default:
                throw new Exception("Database [{$this->cfg('driver')}] not configured", 500);
        }
        return new PDO($dsn, $this->cfg('user'), $this->cfg('pass'), $options);
    }

    /**
     * Make raw SQL-reqquest using PDOStatement
     */
    public function query(string $request, array $data = []): PDOStatement
    {
        $this->statement = $this->connect()->prepare($request);
        $this->statement->execute($data);
        $this->statementErrors();
        return $this->statement;
    }

    private function statementErrors()
    {
        $errCode = $this->statement->errorCode();
        if (!(int)$errCode) {
            return;
        }
        pre([
            'PDOStatement::errorCode()' => $this->statement->errorCode(),
            'PDOStatement::errorInfo()' => $this->statement->errorInfo()
        ]);
        die;
    }

    /**
     * Make SELECT sql-request and get the first row from the table
     */
    public function selectFirst(string $request, array $data = []): array
    {
        $cache_request = $this->bindVariables($request, $data);
        $query = $this->getCacheQuery($cache_request, false);
        if (empty($query)) {
            $row = $this->query($request, $data)->fetch();
            $query = $this->setCacheQuery(
                $cache_request,
                [0 => $row ? $row : []]
            );
            $query =  $query[0];
        }
        return $query;
    }

    /**
     * Make SELECT sql-request and get all founded rows from the table
     */
    public function select(string $request, array $data = []): array
    {
        $cache_request = $this->bindVariables($request, $data);
        $query = $this->getCacheQuery($cache_request);
        return empty($query) ?
            $this->setCacheQuery(
                $cache_request,
                $this->query($request, $data)->fetchAll()
            ) : $query;
    }

    /**
     * Make UPDATE / DELETE sql-request and get count of changes
     */
    public function change(string $request, array $data = []): int
    {
        $cache_request = $this->bindVariables($request, $data);
        $this->markupCacheToUpdate($cache_request);
        return $this->query($request, $data)->rowCount();
    }

    /**
     * Make INSERT sql-request and get the last inserted primary key
     * 
     * @return string last inserted id on success
     */
    public function insert(string $request, array $data = [], ?string $last_insert_id_name = null): string|false
    {
        $cache_request = $this->bindVariables($request, $data);
        $this->markupCacheToUpdate($cache_request);
        $this->query($request, $data);
        return $this->connect()->lastInsertId($last_insert_id_name);
    }
}
