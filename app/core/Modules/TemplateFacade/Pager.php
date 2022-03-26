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
    
    public function tpl(): Element
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
        if ($this->current_page > 0) {
            $items['prev'] = $this->generateItemData(
                $this->current_page - 1,
                img('/images/icons/arrow-thin.svg')->addClass('pager__arrow-img'),
                false
            );
            $items['prev']['class'] = 'pager__link_prev';
        }
        for ($i = 0; $i < $this->total_pages; $i++) {
            $data = [
                'label' => '...',
                'url' => null,
                'item_class' => 'mobile-hidden'
            ];
            $ii = $i;
            if (
                $i < ($this->current_page - 2)
                && $i > 0 && $i !== 1
            ) {
                $ii = 1;
            } else if (
                $i > ($this->current_page + 2)
                && $i < ($this->total_pages - 1)
                && ($this->current_page + 3) !== ($this->total_pages - 2)
            ) {
                $ii = $this->total_pages - 2;
            } else {
                $data = $this->generateItemData($i);
            }
            $items[$ii] = $data;
        }
        if ($this->current_page < $this->total_pages - 1) {
            $items['next'] = $this->generateItemData(
                $this->current_page + 1,
                img('/images/icons/arrow-thin.svg')->addClass('pager__arrow-img'),
                false
            );
            $items['next']['class'] = 'pager__link_next';
        }
        $this->tpl()->set('items', $items);
        return;
    }

    protected function generateItemData(int $i, $label = null, bool $mobile_hidden = true): array
    {
        $data = [
            'label' => is_null($label) ? $i + 1 : $label,
            'url' => null
        ];
        if ($i !== $this->current_page) {
            $data['url'] = app()->router()->getCurrentUrl(['page' => $i]);
            if ($i > 0 && $i < ($this->total_pages - 1) && $mobile_hidden) {
                $data['item_class'] = 'mobile-hidden';
            }
        } else {
            $data['class'] = 'active';
        }
        return $data;
    }
}
