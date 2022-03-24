<?php

namespace Blog\Mediators;

class AjaxResponse
{
    protected string $response;
    protected array $data;

    /**
     * @param string $response ajax response message text
     */
    public function __construct(
        ?string $response = null,
        protected int $code = 200
    ) {
        $this->setResponse($response);
    }

    public function __toString(): string
    {
        return (string)$this->send();
    }

    public function send(): string
    {
        $output = [
            'status' => $this->getCode(),
            'response' => $this->getResponse()
        ];
        $data = $this->getData();
        if (!empty($data)) {
            $output['data'] = $data;
        }
        return json_encode($output);
    }

    public function set(AjaxResponse $response): void
    {
        $this->setCode($response->getCode());
        $this->setResponse($response->getResponse());
        $this->setData($response->getData());
    }
    
    /**
     * @param string $response ajax response message text
     */
    public function setResponse(?string $response): void
    {
        if (!is_null($response)) {
            $this->response = $response;
        }
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

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data ?? [];
    }
}
