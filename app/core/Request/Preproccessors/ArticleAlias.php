<?php

namespace Blog\Request\Preproccessors;

use Blog\Modules\Entity\Article;
use Blog\Request\RequestPrototype;

#[\Attribute]
class ArticleAlias implements PreproccessorInterface
{
    public function format(string $field_name, RequestPrototype $request): string
    {
        $alias = $request->raw($field_name) ?
            $request->raw($field_name)
            : kebabCase($request->raw('title'), true);
        if (Article::isAliasExists($alias)) {
            $alias .= '_' . Article::getNewId();
        }
        return $alias;
    }
}
