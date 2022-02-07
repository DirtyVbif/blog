<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Request\BaseRequest;

class Comment extends BaseEntity
{
    protected function setEntityDefaults(): void
    {
        $this->table_name = ['c' => 'comments'];
        $this->table_columns_query = [
            'c' => ['cid', 'pid', 'created', 'name', 'email', 'body', 'status', 'ip'],
            'ac' => ['aid']
        ];
    }

    protected function preprocessSqlSelect(SQLSelect &$sql): void
    {
        $sql->join(table: ['ac' => 'article_comments'], using: 'cid');
        $sql->where(['c.cid' => $this->id()]);
        return;
    }
    
    /**
     * @param \Blog\Request\CommentRequest $data
     */
    public function create(BaseRequest $data): self
    {
        if (!$data->isValid()) {
            return $this;
        }
        $sql = sql_insert($this->getTableName());
        $sql->set(
            [$data->parent_id, time(), $data->name, $data->email, $data->subject, 0, $_SERVER['REMOTE_ADDR']],
            ['pid', 'created', 'name', 'email', 'body', 'status', 'ip']
        );
        $cid = (int)$sql->exe();
        if ($cid) {
            $sql = sql_insert('article_comments');
            $sql->set(
                [$data->article_id, $cid],
                ['aid', 'cid']
            );
            $sql->exe();
        }
        return $this;
    }
}
