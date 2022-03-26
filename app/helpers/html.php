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

/**
 * Normalizes html single classname string.
 * 
 * All whitespaces will be replaced with single undescores `_` symbol.
 * All other non-word and non-numeric symbols will be replaced single with `-` symbol
 */
function normalizeClassname(string $classname): string
{
    $classname = preg_replace(
        [
            '/^[^a-z]*/i',
            '/[^a-z0-9]*$/i',
            '/\s+/'
        ],
        ['', '', '_'],
        $classname
    );
    $classname = preg_replace('/\W+/', '-', $classname);
    return strtolower($classname);
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
    $value = session()->get(\Blog\Request\RequestPrototype::SESSID . '/' . $name);
    if ($as_attribute && $value) {
        return new \Twig\Markup(" value=\"{$value}\"", CHARSET);
    }
    return $value;
}

function strip_attributes(string $html): string
{
    // /            # Start Pattern
    // <            # Match '<' at beginning of tags
    // (            # Start Capture Group $1 - Tag Name
    // [a-z]        # Match 'a' through 'z'
    // [a-z0-9]*    # Match 'a' through 'z' or '0' through '9' zero or more times
    // )            # End Capture Group
    // ([^>]*?)     # Capture Group $2 - Match anything other than '>', Zero or More times, not-greedy (wont eat the /)
    // (\/?)        # Capture Group $3 - '/' if it is there
    // >            # Match '>'
    // /is          # End Pattern - Case Insensitive & Multi-line ability
    $pattern = '/<([a-z][a-z0-9]*)([^>]*?)(\/?)>/si';
    return preg_replace($pattern, '<$1$3>', $html);
}

function usesvg(string $href, array $options = [])
{
    $class = ($options['class'] ?? false) ? ' class="' . $options['class'] . '"' : '';
    $output = '<svg' . $class . '><use href="' . $href . '"></use></svg>';
    if ($options['markup'] ?? true) {
        $output = new \Twig\Markup($output, CHARSET);
    }
    return $output;
}

/**
 * Converts string to BEM-model modificator `_mod-name`
 */
function bemmod(string $mod): string
{
    return '_' . kebabCase($mod);
}

/**
 * Converts string to BEM-model element `__element-name`
 */
function bemelem(string $element): string
{
    return '__' . kebabCase($element);
}
