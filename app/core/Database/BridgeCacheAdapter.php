<?php

namespace Blog\Database;

use Blog\Modules\Cache\CacheSql;

class BridgeCacheAdapter
{
    protected const LOGID = 'SQL-Cache-Adapter';
    protected const PROTECTED_TABLES = [
        'users_sessions'
    ];

    protected function cache(): CacheSql
    {
        return app()->cache()->sql();
    }

    /**
     * @param array $pdo_data with `key => value` pairs for PDO sql request string
     * if @param string $sql_request requires value binding.
     * @param null $pdo_data if @param string $sql_request doesn't require value binding.
     * 
     * @return array with data from cache
     * @return false if no cache data available
     */
    public function get(SQLSelect $sql): array|false
    {
        if ($this->isProtectedRequest($sql)) {
            return false;
        }
        $request = $sql->bind($sql->raw(), $sql->data());
        $query = $this->cache()->get($request);
        if (!$query) {
            consoleLog(self::LOGID, "There is no actual cache for SQL REQUEST: \"{$request}\".");
        } else {
            consoleLog(self::LOGID, "Getting cached data for SQL REQUEST: \"{$request}\".");
        }
        return $query;
    }

    /**
     * Set new cache data (@param array $query_data) for provided SQL QUERY REQUEST STRING (@param string $sql_request)
     * 
     * @param array $pdo_data with `key => value` pairs for PDO sql request string
     * if @param string $sql_request requires value binding.
     * @param null $pdo_data if @param string $sql_request doesn't require value binding.
     * 
     * @return array provided @param array $query_data
     */
    public function set(SQLSelect $sql, array $query_data): array
    {
        if (!empty($query_data) && !$this->isProtectedRequest($sql)) {
            $request = $sql->bind($sql->raw(), $sql->data());
            $this->cache()->set(
                $request,
                $query_data,
                $sql->getRequestTables()
            );
            consoleLog(self::LOGID, "Storing new cache data for SQL REQUEST: \"{$request}\".");
        }
        return $query_data;
    }

    /**
     * Update cache for specified SQL REQUEST if it's required update.
     * 
     * Check request for statement that required cache update (SQL UPDATE | INSERT | DELETE):
     * - Method detectes table names that was changed in provided @param string $sql_request
     * - Then table names passing into cache module that flushing cache for specified tables
     * 
     * @param array $pdo_data with `key => value` pairs for PDO sql request string
     * if @param string $sql_request requires value binding.
     * @param null $pdo_data if @param string $sql_request doesn't require value binding.
     */
    public function update(SQLInsert|SQLUpdate $sql): void
    {
        if (!$this->isProtectedRequest($sql)) {
            $this->cache()->refreshTables($sql->getRequestTables());
            consoleLog(self::LOGID, "Refreshing cache for tables: `" . implode('`, `', $sql->getRequestTables()) . "`.");
        }
        return;
    }

    /**
     * Checks request (@param string $request) if it has protected tables
     * 
     * @return true if request has SELECT STATEMENT and request contains protected tables
     * @return false otherwise
     */
    protected function isProtectedRequest(SQLAbstractStatement $sql): bool
    {
        foreach (self::PROTECTED_TABLES as $table) {
            if (in_array($table, $sql->getRequestTables())) {
                return true;
            }
        }
        return false;
    }
}
