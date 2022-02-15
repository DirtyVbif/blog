<?php

namespace Blog\Modules\View;

abstract class BaseView
{
    /**
     * @return array of enitities with limited (0 - no limit) count and specified entity view mode
     */
    abstract public function preview(int $limit, string $view_mode): array;

    /**
     * @return object with items array and pager fields e.g.: `{'items' => [...], 'pager' => PagerFacadeElement::object}`
     */
    abstract public function view(): object;
}
