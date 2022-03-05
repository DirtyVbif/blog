<?php

namespace BlogForge\Services;

class Make extends ServicePrototype implements ServiceInterface
{
    protected const OPTIONS = [
        '-a' => 'abstract class',
        '-t' => 'trait',
        '-i' => 'interface'
    ];

    public function run(?string $method): void
    {
        $method = lcfirst(pascalCase($method));
        if (!method_exists($this, $method)) {
            $this->forge->setError("There is no available methods like `{$method}` for `make` action.");
            return;
        }
        $this->{$method}();
        return;
    }

    public function default(): void
    {
        $this->forge->setNotice("there is no default method for `make` action");
    }

    public function class(): void
    {
        $name = $GLOBALS['argv'][2] ?? null;
        if (!$name) {
            $this->forge->setError("No classname provided.");
            return;
        }
        $classname = $this->normalizeClassname($name);
        $namespace = count($classname['namespace']) ? implode('\\', $classname['namespace']) : null;
        $content = $this->generateScriptContent($classname['class'], $namespace);
        $filename = COREDIR . implode('/', $classname['namespace']) . "/{$classname['class']}.php";
        $file = f($filename)->content($content)->save();
        $result = $file->exists();
        if (!$result) {
            $this->forge->setError("Failed to create `{$filename}` file.");
        } else {
            $this->forge->setSuccess("File `{$filename}` created.");
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
