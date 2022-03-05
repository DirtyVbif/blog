<?php

namespace BlogForge\Services;

class Make extends ServicePrototype implements ServiceInterface
{
    protected const OPTIONS = [
        '-a' => 'abstract class',
        '-t' => 'trait',
        '-i' => 'interface'
    ];

    public function default(): void
    {
        forge()->setNotice("there is no default method for `make` action");
    }

    public function run(?string $method): void
    {
        $method = lcfirst(pascalCase($method ?? ''));
        if (!method_exists($this, $method)) {
            forge()->setError("There is no available methods like `{$method}` for `make` action.");
            return;
        }
        $this->{$method}();
        return;
    }

    public function class(): void
    {
        $name = $GLOBALS['argv'][2] ?? null;
        if (!$name) {
            forge()->setError("No classname provided.");
            return;
        }
        $classname = $this->normalizeClassname($name);
        $namespace = count($classname['namespace']) ? implode('\\', $classname['namespace']) : null;
        $content = $this->generateScriptContent($classname['class'], $namespace);
        $filename = "{$classname['class']}.php";
        $directory = strSuffix(COREDIR . implode('/', $classname['namespace']), '/');
        $file = f($filename, $directory);
        $file->content($content);
        $file->save();
        $result = $file->exists();
        if (!$result) {
            forge()->setError("Failed to create `{$directory}{$filename}` file.");
        } else {
            forge()->setSuccess("File `{$directory}{$filename}` created.");
        }
        return;
    }

    protected function generateScriptContent(string $classname, ?string $namespace = null): string
    {
        $option = $GLOBALS['argv'][3] ?? null;
        $construct_word = self::OPTIONS[$option] ?? 'class';
        $content = "<?php\n\n";
        if ($namespace) {
            $content .= "namespace Blog\\{$namespace};\n\n";
        } else {
            $content .= "namespace Blog;";
        }
        $content .= "{$construct_word} {$classname}\n{\n\n}\n";
        return $content;
    }
}
