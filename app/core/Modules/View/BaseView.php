<?php

namespace Blog\Modules\View;

abstract class BaseView
{
    abstract public function preview(int $limit, string $view_mode);

    /**
     * @return object with items array and pager fields e.g. `{'items' => [...], 'pager' => PagerFacadeElement::object}`
     */
    abstract public function view(): object;
}
