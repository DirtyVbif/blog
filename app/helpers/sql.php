<?php

use Blog\Database\Bridge;
use Blog\Database\SQLInsert;
use Blog\Database\SQLSelect;
use Blog\Database\SQLUpdate;

function sql(): Bridge
{
    return app()->sql();
}

/**
 * Returns an object of type `DMPF\Database\SQLSelect`
 * 
 * @param array|null $columns if is set than used for @method Blog\Database\SQLSelect::columns()
 * @param string|array|null $from if is set than used for @method Blog\Database\SQLSelect::from()
 */
function db_select(?array $columns = null, string|array|null $from = null): SQLSelect
{
    $statement = new SQLSelect;
    if (!is_null($columns)) {
        $statement->columns($columns);
    }
    if (!is_null($from)) {
        $statement->from($from);
    }
    return $statement;
}

function db_update(?array $set = null, ?string $table = null): SQLUpdate
{
    $statement = new SQLUpdate;
    if (!is_null($set)) {
        $statement->set($set);
    }
    if (!is_null($table)) {
        $statement->table($table);
    }
    return $statement;
}

function db_delete(?string $from = null): SQLUpdate
{
    return db_update(table: $from)->setDel();
}

function db_insert(?string $table = null): SQLInsert
{
    $statement = new SQLInsert;
    if (!is_null($table)) {
        $statement->into($table);
    }
    return $statement;
}