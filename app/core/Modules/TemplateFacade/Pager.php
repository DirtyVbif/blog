<?php

namespace Blog\Modules\TemplateFacade;

use Blog\Modules\Template\Element;

class Pager extends TemplateFacade
{
    protected int $current_page;
    protected int $total_pages;

    public function __construct(
        protected int $total_items,
        protected int $items_per_page
    ) {
        $this->setCurrentPage();
        $this->calculateTotalPages();
    }

    protected function setCurrentPage(): void
    {
        $this->current_page = isset($_GET['page']) ? max((int)$_GET['page'], 0) : 0;
        return;
    }

    protected function calculateTotalPages(): void
    {
        /** @var float $count */
        $count = $this->total_items / $this->items_per_page;
        $this->total_pages = ceil($count);
        return;
    }

    /**
     * @return Element
     */
    public function tpl()
    {
        if (!isset($this->tpl)) {
            $tpl = new Element('ul');
            $tpl->setName('elements/pager');
            $this->tpl = $tpl;
        }
        return $this->tpl;
    }

    public function render()
    {
        $this->setTemplateItems();
        return parent::render();
    }

    protected function setTemplateItems(): void
    {
        $items = [];
        for ($i = 0; $i < $this->total_pages; $i++) {
            // TODO: complete pager template
            $items[$i] = [
                'label' => $i + 1
            ];
        }
        $this->tpl()->set('items', $items);
        return;
    }
}
