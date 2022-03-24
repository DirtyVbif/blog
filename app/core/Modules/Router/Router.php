<?php

namespace Blog\Modules\Router;

class Router
{
    use Components\RouterRedirects;

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
        if ($this->isPostRequest() && !$this->isAjaxRequest()) {
            $this->setControllerName('post');
        } else if ($this->isGetRequest() || $this->isAjaxRequest()) {
            $this->setControllerName($this->arg(1) ?? 'front');
        } else {
            die('unknown request method.');
        }
        return;
    }

    protected function setControllerName(string $controller_name): void
    {
        $controller_name = urldecode($controller_name);
        $controller_name = strtolower($controller_name);
        $this->data['controller'] = ucfirst($controller_name) . 'Controller';
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

    public function isAjaxRequest(): bool
    {
        return $this->arg(1) === 'ajax';
    }

    public function isGetRequest(): bool
    {
        return $this->get('method') === 'GET';
    }

    public function isPostRequest(): bool
    {
        return $this->get('method') === 'POST';
    }

    public function isHome(): bool
    {
        return $this->get('url') === '/';
    }

    /**
     * Get current relative offset of specified deep level
     * 
     * @param int $level min 1
     */
    public function level(int $level): string
    {
        $level = $level < 1 ? 1 : $level;
        $url = '';
        for ($i = 1; $i <= $level; $i++) {
            if (!$arg = $this->arg($i)) {
                break;
            }
            $url .= '/' . $arg;
        }
        return $url ? $url : '/';
    }

    public function domain(): string
    {
        return $this->get('domain');
    }

    public function host(): string
    {
        return $this->get('scheme') . '://' . $this->get('domain');
    }

    public function storeLastUrl(): void
    {
        if ($this->isGetRequest() && !$this->isAjaxRequest()) {
            session()->set('router/previous-url', $this->url());
        }
        return;
    }
}
