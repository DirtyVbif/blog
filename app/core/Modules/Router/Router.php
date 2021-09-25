<?php

namespace Blog\Modules\Router;

class Router
{
    protected array $data = [];
    protected int $status = 200;
    protected string $default_langcode = 'en';

    public function __construct()
    {
        $this->parseRequest();
        $this->data['langcode'] = app()->config()->langcode ?? $this->default_langcode;
    }

    protected function parseRequest(): void
    {
        $this->data['domain'] = $_SERVER['HTTP_HOST'];
        $this->data['method'] = $_SERVER['REQUEST_METHOD'];
        $this->data['scheme'] = $_SERVER['REQUEST_SCHEME'];
        $this->data['url'] = urldecode($_SERVER['REDIRECT_URL'] ?? '/');
        $this->data['params'] = $_GET ?? [];
        $this->parseUrlArguments();
        $c_name = strtolower($this->arg(1) ?? 'front');
        $this->data['controller'] = ucfirst($c_name) . 'Controller';
        return;
    }

    protected function parseUrlArguments(): void
    {
        $this->data['args'] = [];
        $i = 1;
        foreach (explode('/', $this->url()) as $arg) {
            if (!$arg) {
                continue;
            }
            $this->data['args'][$i] = $arg;
            $i++;
        }
        return;
    }

    public function url(): string
    {
        return $this->data['url'];
    }

    /**
     * @param int $n url argument position number. First position = 1.
     * For url `/foo/bar/baz` position numbers are `[1 => foo, 2 => bar, 3 => baz]`
     */
    public function arg(int $n): ?string
    {
        $n = $n < 1 ? 1 : $n;
        return $this->data['args'][$n] ?? null;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function data(): object
    {
        return (object)$this->data;
    }

    public function get(string $key)
    {
        return $this->data()->$key;
    }

    public function getLangcode(): string
    {
        return $this->get('langcode');
    }
}
