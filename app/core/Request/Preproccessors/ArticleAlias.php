<?php

namespace Blog\Request\Preproccessors;

use Blog\Modules\Entity\ArticlePrototype;
use Blog\Request\RequestPrototype;

#[\Attribute]
class ArticleAlias implements PreproccessorInterface
{
    public function format(string $field_name, RequestPrototype $request): string
    {
        $alias = $request->raw($field_name) ?
            $request->raw($field_name)
            : kebabCase($request->raw('title'), true);
        if (ArticlePrototype::isAliasExists($alias)) {
            $alias .= '_' . ArticlePrototype::getNewId();
        }
        return $alias;
    }
}
