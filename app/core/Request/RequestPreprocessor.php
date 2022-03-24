<?php

namespace Blog\Request;

use Blog\Modules\Entity\Article;

class RequestPreprocessor
{
    public static function articleAlias(string $field_support_name, string $field_target_name, RequestPrototype $request)
    {
        $alias = $request->raw($field_target_name) ?
            $request->raw($field_target_name)
            : kebabCase($request->raw($field_support_name), true);
        if (Article::isAliasExists($alias, $request->raw('entity_id'))) {
            $alias .= '_' . Article::getNewId();
        }
        return $alias;
    }

    public static function defaultValue($value, string $field_name, RequestPrototype $request)
    {
        return $request->raw($field_name) ? $request->raw($field_name) : $value;
    }
}
