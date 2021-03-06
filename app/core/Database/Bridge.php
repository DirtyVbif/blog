<?php

namespace Blog\Database;

use Blog\Client\User;
use DateTime;
use PDO;
use PDOStatement;

class Bridge
{
    private const LOGID = 'sql';
    private const SESID = 'DB-Bridge';

    private object $config;
    private PDO $connection;
    private PDOStatement $statement;
    private BridgeCacheAdapter $cache_adapter;

    public function __construct()
    {
        $this->config = (object)app()->env()->DB;
        if (
            $this->connect()->inTransaction()
            && $time = session()->get(self::SESID . '/transaction/start')
        ) {
            msgr()->warning(
                'Database is in transacrion now. Timestamp: ' . $time,
                access_level: User::ACCESS_LEVEL_MASTER
            );
        }
        return $this;
    }

    /**
     * cad - cache adapter
     */
    private function cad(): BridgeCacheAdapter
    {
        if (!isset($this->cache_adapter)) {
            $this->cache_adapter = new BridgeCacheAdapter;
        }
        return $this->cache_adapter;
    }

    private function cfg(string $key): string
    {
        $key = strtoupper($key);
        return $this->config->{$key};
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
                pre("Database [{$this->cfg('driver')}] is not configured");
                exit;
        }
        return new PDO($dsn, $this->cfg('user'), $this->cfg('pass'), $options);
    }

    /**
     * Make raw SQL-reqquest using PDOStatement
     */
    public function query(SQLAbstractStatement|string $sql, array $data = []): PDOStatement
    {
        if (!is_string($sql)) {
            $data = $sql->data();
            $sql = $sql->raw();
        }
        $this->statement = $this->connect()->prepare($sql);
        $this->statement->execute($data);
        $this->statementErrors($sql);
        return $this->statement;
    }

    private function statementErrors(string $sql)
    {
        $errCode = $this->statement->errorCode();
        if (!(int)$errCode) {
            return;
        }
        pre([
            'PDOStatement::errorCode()' => $this->statement->errorCode(),
            'PDOStatement::errorInfo()' => $this->statement->errorInfo(),
            'SQL-REQUEST' => $sql
        ]);
        exit;
    }

    /**
     * Make SELECT sql-request and get the first row from the table
     */
    public function selectFirst(SQLSelect $sql): array
    {
        if (!$sql->cacheAvailable()) {
            return $this->query($sql)->fetch();
        }
        $query = $this->cad()->get($sql);
        if (empty($query)) {
            $new_cache_data = $this->query($sql)->fetch();
            $new_cache_data = $new_cache_data ? [0 => $new_cache_data] : [];
            $query = $this->cad()->set($sql, $new_cache_data);
        }
        return $query ? ($query[0] ?? []) : [];
    }

    /**
     * Make SELECT sql-request and get all founded rows from the table
     */
    public function select(SQLSelect $sql): array
    {
        if (!$sql->cacheAvailable()) {
            return $this->query($sql)->fetchAll();
        }
        $query = $this->cad()->get($sql);
        return empty($query) ?
            $this->cad()->set(
                $sql,
                $this->query($sql)->fetchAll()
            ) : $query;
    }

    /**
     * Make UPDATE / DELETE sql-request and get count of changes
     */
    public function change(SQLUpdate|SQLInsert $sql): int
    {
        if ($sql->cacheAvailable()) {
            $this->cad()->update($sql);
        }
        return $this->query($sql)->rowCount();
    }

    /**
     * Make INSERT sql-request and get the last inserted primary key
     * 
     * @return string last inserted id on success
     */
    public function insert(SQLInsert $sql): string|false
    {
        if ($sql->cacheAvailable()) {
            $this->cad()->update($sql);
        }
        $this->query($sql);
        return $this->connect()->lastInsertId($sql->lastInsertIdName());
    }

    public function startTransation(): void
    {
        if (!$this->connect()->inTransaction()) {
            $date = DateTime::createFromFormat('U.u', microtime(true));
            session()->set(self::SESID . '/transaction/start', $date->format('Y-m-d H:i:s.u'));
            $this->connect()->beginTransaction();
            consoleLog(self::LOGID, 'Database transaction started;');
        }
        return;
    }

    public function commit(bool $rollback = false): void
    {
        if ($rollback) {
            $this->rollback();
        } else if ($this->connect()->inTransaction()) {
            session()->unset(self::SESID . '/transaction/start');
            $this->connect()->commit();
            consoleLog(self::LOGID, 'Database transaction completed;');
        }
        return;
    }

    public function rollback(): void
    {
        if ($this->connect()->inTransaction()) {
            session()->unset(self::SESID . '/transaction/start');
            $this->connect()->rollBack();
            consoleLog(self::LOGID, 'Database transaction rolled back;');
        }
        return;
    }
}
