<?php

namespace Blog\Modules\Router\Components;

trait RouterRedirects
{
    protected array $REDIRECTS = [
        '<front>' => 'getHomeUrl',
        '<home>' => 'getHomeUrl',
        '<current_url>' => 'getCurrentUrl',
        '<current>' => 'getCurrentUrl',
        '<current_offset>' => 'getCurrentOffset',
        '<previous>' => 'getPreviousUrl'
    ];

    /**
     * Redirects to specified location.
     * 
     * @param string|int $location can be as @var int of required offset level of current url. 
     * Or as @var string as url. Or special constant can be used:
     * * `<front>`              - or `<home>` - redirect to home page;
     * * `<current_url>`        - or `<current>` - redirect to current relative offset without GET parameters
     * * `<previous>`           - redirect on previous location
     * 
     * @param int $status [optional] HTTP-redirect status. 302 by default
     */
    public function redirect(string|int $location, int $status = 302): void
    {
        if (is_numeric($location)) {
            $location = app()->router()->level($location);
        } else {
            $location = $this->getUrl($location);
        }
        header("Location: $location", true, $status);
        exit;
    }

    public function getUrl(string $path, array $parameters = []): string
    {
        if ($method = $this->REDIRECTS[$path] ?? false) {
            $url = $this->$method($parameters);
        } else {
            $url = $path !== '/' ? strSuffix($path, '/', true) : $path;
            $url .= $this->implodeGetParameters($parameters);
        }
        return $url;
    }

    public function getHomeUrl(array $parameters = []): string
    {
        return '/' . $this->implodeGetParameters($parameters);
    }

    public function getCurrentUrl(array $parameters = []): string
    {
        $url = app()->router()->get('url');
        $parameters += app()->router()->get('params');
        $url .= $this->implodeGetParameters($parameters);
        return $url;
    }

    public function getPreviousUrl(array $parameters = []): string
    {
        return urldecode($_SERVER['HTTP_REFERER']);
    }

    public function getCurrentOffset(array $parameters = []): string
    {
        $url = app()->router()->get('url');
        return $url;
    }

    protected function implodeGetParameters(array $parameters): string
    {        
        $pairs = [];
        foreach ($parameters as $name => $value) {
            $pairs[] = $name . '=' . $value;
        }
        return empty($pairs) ? '' : '?' . implode('&', $pairs);
    }
}