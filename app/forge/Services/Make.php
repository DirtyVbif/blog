<?php

namespace BlogForge\Services;

use Blog\Modules\FileSystem\Folder;

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

    protected function createClassFile(string $filename, string $directory, string $content): bool
    {
        $folder = new Folder($directory);
        $folder->create();
        if (!$folder->exists()) {
            forge()->setError("Failed to create `{$directory}` directory.");
            return false;
        } else {
            Folder::chmod($folder->path(), true);
        }
        $file = f($filename, $folder->path());
        $file->content($content);
        $file->save();
        if (!$file->exists()) {
            forge()->setError("Failed to create `{$directory}{$filename}` file.");
            return false;
        }
        forge()->setSuccess("File `{$directory}{$filename}` created.");
        return true;
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
        $content = $this->generateClassFileContent($classname['class'], $namespace);
        $filename = "{$classname['class']}.php";
        $directory = strSuffix(COREDIR . implode('/', $classname['namespace']), '/');
        $this->createClassFile($filename, $directory, $content);
        return;
    }

    /**
     * Alias method for @method library()
     */
    public function lib(): void
    {
        $this->library();
        return;
    }

    public function library(): void
    {
        $name = $GLOBALS['argv'][2] ?? null;
        if (!$name) {
            forge()->setError("No library name provided.");
            return;
        }
        $class = $this->normalizeClassname($name);
        $classname = $namespace = $class['class'];
        $options = [
            'extends' => '\Blog\Modules\Library\AbstractLibrary',
            'vendor' => 'blog-library'
        ];
        $content = $this->generateClassFileContent($classname, $namespace, $options);
        $filename = "{$classname}.php";
        $directory = LIBDIR . "{$namespace}/";
        $this->createClassFile($filename, $directory, $content);
        return;
    }

    protected function generateClassFileContent(string $classname, ?string $namespace = null, array $options = []): string
    {
        $vendor = pascalCase($options['vendor'] ?? 'blog');
        $option = $GLOBALS['argv'][3] ?? null;
        $construct_word = self::OPTIONS[$option] ?? 'class';
        $content = sprintf(
            "<?php\n\nnamespace {$vendor}%s;\n\n",
            ($namespace ? "\\{$namespace}" : '')
        );
        $content .= "{$construct_word} {$classname}%s\n{\n\n}\n";
        $implementation = '';
        if ($ext = $options['extends'] ?? false) {
            $implementation .= " extends {$ext}";
        }
        $content = sprintf($content, $implementation);
        return $content;
    }
}
