<?php

/**
 * Parsing provided classlist into string with prefix or/and suffix if specified
 * 
 * @param string[]|string $classlist can be a string of classes separeated by whitespace or an array of classes:
 * * `(string) => "class_name_1 class_name_2 ..."`;
 * * `(array) => ["class_name_1", "class_name_2", ...]`.
 * 
 * @param string $prefix [optional] will be prepend to each class name in classlist if provided.
 * 
 * @param string $suffix [optional] will be append to each class name in classlist if provided.
 */
function classlistToString(array|string $classlist, string|array $prefix = '', string|array $suffix = ''): string
{
    if (!empty($prefix) || !empty($suffix)) {
        if (is_string($classlist)) {
            $classlist = preg_split('/\s+/', $classlist);
        }
        foreach ($classlist as $i => $class) {
            if (!$class) {
                unset($classlist[$i]);
                continue;
            }
            if (!empty($prefix) && is_array($prefix)) {
                $classlist_part = [];
                foreach ($prefix as $p) {
                    if (empty($p)) {
                        continue;
                    }
                    $classlist_part[] = $p . $class;
                }
                $classlist[$i] = implode(' ', $classlist_part);
            } else if (!empty($prefix)) {
                $classlist[$i] = $prefix . $class;
            }
            if (!empty($suffix) && is_array($suffix)) {
                $classlist_part = [];
                foreach ($suffix as $s) {
                    if (empty($s)) {
                        continue;
                    }
                    $classlist_part[] = $class . $s;
                }
                $classlist[$i] = implode(' ', $classlist_part);
            } else if (!empty($suffix)) {
                $classlist[$i] = $class . $suffix;
            }
        }
    }
    if (is_array($classlist)) {
        $classlist = implode(' ', $classlist);
    }
    return $classlist;
}

function img(string $src): \Blog\Modules\TemplateFacade\Image
{
    $img = new \Blog\Modules\TemplateFacade\Image($src);
    return $img;
}

function csrf(bool $render = true)
{
    if ($render) {
        return new \Twig\Markup(app()->csrf()->render(), CHARSET);
    }
    return app()->csrf();
}

/**
 * Get last value for field by name
 */
function old(string $name, bool $as_attribute = false)
{
    // TODO: rebuild function to get required value via RequestPrototype::class method
    $value = session()->get(\Blog\Request\RequestPrototype::SESSID . '/' . $name);
    if ($as_attribute && $value) {
        return new \Twig\Markup(" value=\"{$value}\"", CHARSET);
    }
    return $value;
}
