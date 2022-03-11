<?php

namespace Blog\Client;

class UserAgent
{
    protected const SESSID = 'user-agent-data';

    protected array $data;
    
    public function __construct()
    {
        $this->setUserAgentData();
    }
    
    protected function setUserAgentData(): void
    {
        $this->data = session()->get(self::SESSID) ?? [];
        if (empty($this->data)) {
            $parser = \UAParser\Parser::create();
            $result = $parser->parse($_SERVER['HTTP_USER_AGENT']);
            $this->data = [
                'browser' => (string)$result->ua,
                'platform' => (string)$result->os,
                'device' => (string)$result->device,
                'original_string' => $_SERVER['HTTP_USER_AGENT'],
                'hash' => md5($_SERVER['HTTP_USER_AGENT'])
            ];
            session()->set(self::SESSID, $this->data);
        }
        return;
    }

    /**
     * Get browser user agent information.
     * 
     * @param string|null $key [optional] Any allowed browser info key:
     * * `browser`
     * * `platform`
     * * `device`
     * * `original_string`
     * * `hash`
     * 
     * @return object|string|null $data
     * * return @var object of user agent data if no @param string $key provided;
     * * return @var ?string if @param string $key provided;
     */
    public function get(?string $key = null): object|string|null
    {
        if (!isset($this->data)) {
            $this->setUserAgentData();
        }
        return $key ? $this->data[$key] ?? null : (object)$this->data;
    }

    public function hash(): string
    {
        return $this->get('hash');
    }

    public function browser(): string
    {
        return $this->get('browser');
    }

    public function platform(): string
    {
        return $this->get('platform');
    }

    public function device(): string
    {
        return $this->get('device');
    }

    public function original(): string
    {
        return $this->get('original');
    }
}
