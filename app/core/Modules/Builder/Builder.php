<?php

namespace Blog\Modules\Builder;

class Builder
{
    public function preparePage(): void
    {
        app()->page()->setTitle(app()->controller()->getTitle());
        return;
    }
}
