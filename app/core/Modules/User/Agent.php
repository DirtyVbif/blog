<?php

namespace Blog\Modules\User;

class Agent
{
    protected array $data;

    /**
     * Get prepared user agent data
     * 
     * @return array|stdClass $data `['hash', 'browser', 'platform']`
     */
    public function getData(bool $object = false)
    {
        $data = [
            'hash' => $this->getHash(),
            'browser' => $this->get('browser').' '.$this->get('version'),
            'platform' => $this->get('device_type').' '.$this->get('platform')
        ];
        return $object ? (object)$data : $data;
    }

    /**
     * Get browser info.
     * 
     * @param string|null $key [optional]
     * 
     * Any allowed browser info @return array key. 
     * * `browser_name_regex`
     * * `browser_name_pattern`
     * * `parent`
     * * `platform`
     * * `comment`
     * * `browser`
     * * `version`
     * * `device_type`
     * * `ismobiledevice`
     * * `istablet`
     */
    public function get(?string $key = null, bool $return_array = true)
    {
        if (!isset($this->data)) {
            $this->data = get_browser(null, $return_array);
        }
        return $key ? $this->data[$key] ?? null : $this->data;
    }

    public function getHash(): string
    {
        return md5($this->get('browser_name_pattern'));
    }
}
