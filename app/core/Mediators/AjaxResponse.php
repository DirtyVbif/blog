<?php

namespace Blog\Mediators;

class AjaxResponse
{
    protected string $response;
    protected int $code = 200;

    /**
     * @param string $response ajax response message text
     */
    public function __construct(
        ?string $response = null
    ) {
        $this->setResponse($response);
    }

    public function __toString()
    {
        return (string)$this->send();
    }

    /**
     * @param string $response ajax response message text
     */
    public function setResponse(?string $response)
    {
        if (!is_null($response)) {
            $this->response = $response;
        }
        return;
    }

    public function getResponse(): ?string
    {
        return $this->response ?? null;
    }

    /**
     * @param int $code response http status code.
     * Available codes: @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
     */
    public function setCode(int $code): void
    {
        if (!preg_match('/\d{3}/', $code)) {
            return;
        }
        $this->code = $code;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function send(): string
    {
        return json_encode([
            'status' => $this->getCode(),
            'response' => $this->getResponse()
        ]);
    }

    public function set(AjaxResponse $response): void
    {
        $this->setCode($response->getCode());
        $this->setResponse($response->getResponse());
        return;
    }
}
