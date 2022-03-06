<?php

namespace Blog\Modules\Mailer;

class EMail
{
    public const TWIG_TPL = '@mailer/message.html.twig';
    public const SRC_DIR = 'app/core/Modules/Mailer/src/';

    protected array $data;
    protected string $html_body;
    protected array $headers;
    
    public function __construct(
        protected string $to,
        protected string $from,
        protected string $subject
    ) {
        $this->headers = [
            'From' => $from,
            'Reply-To' => $from,
            'X-Mailer' => 'PHP/' . phpversion()
        ];
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
        return;
    }

    protected function getData(): array
    {
        return $this->data ?? [];
    }

    public function getHtmlBody(): string
    {
        app()->twig_add_namespace(self::SRC_DIR, 'mailer');
        if (!isset($this->html_body)) {
            $this->html_body = app()->twig()->render(self::TWIG_TPL, $this->getData());
        }
        return $this->html_body;
    }

    public function getHeaders(): array
    {
        return $this->headers ?? [];
    }
}
