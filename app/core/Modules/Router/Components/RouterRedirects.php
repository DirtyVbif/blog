<?php

namespace Blog\Modules\Router\Components;

trait RouterRedirects
{
    protected array $REDIRECTS = [
        '<front>' => 'getHomeUrl',
        '<home>' => 'getHomeUrl',
        '<current_url>' => 'getCurrentUrl',
        '<current>' => 'getCurrentUrl',
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
        } else if (isset($this->REDIRECTS[$location])) {
            $location = $this->getPathByConstant($location);
        }
        header("Location: $location", true, $status);
        exit;
    }

    public function getHomeUrl(): string
    {
        return '/';
    }

    public function getCurrentUrl(): string
    {
        return app()->router()->get('url');
    }

    public function getPreviousUrl(): string
    {
        return urldecode($_SERVER['HTTP_REFERER']);
    }
}