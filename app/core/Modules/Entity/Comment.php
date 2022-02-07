<?php

namespace Blog\Modules\Entity;

use Blog\Database\SQLSelect;
use Blog\Modules\Template\Element;
use Blog\Request\BaseRequest;

class Comment extends BaseEntity
{
    public function tpl()
    {
        if (!isset($this->tpl)) {
            $this->tpl = new Element;
        }
        return $this->tpl;
    }

    protected function setEntityDefaults(): void
    {
        $this->table_name = ['c' => 'comments'];
        $this->table_columns_query = [
            'c' => ['cid', 'pid', 'created', 'name', 'email', 'body', 'status', 'ip'],
            'ac' => ['aid']
        ];
    }

    protected function queryDataFromStorage(SQLSelect $sql): array
    {
        $sql->join(table: ['ac' => 'article_comments'], using: 'cid');
        $sql->where(['c.cid' => $this->id()]);
        return $sql()->first();
    }
    
    /**
     * @param \Blog\Request\CommentRequest $data
     */
    public function create(BaseRequest $data): bool
    {
        if (!$data->isValid()) {
            return false;
        }
        $sql = sql_insert('comments');
        $pid = $data->parent_id ? $data->parent_id : null;
        $sql->set(
            [$pid, time(), $data->name, $data->email, $data->subject, 0, $_SERVER['REMOTE_ADDR']],
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
            return true;
        }
        return false;
    }
}
