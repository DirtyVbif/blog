<?php

namespace Blog\Request;

class FeedbackRequest extends BaseRequest
{
    protected const FIELD_NAMES = [
        'name' => 'Name',
        'email' => 'E-mail',
        'subject' => 'Message'
    ];

    protected function rules(): array
    {
        return [
            'name' => [
                'type' => 'string',
                'max_length' => 60,
                'required' => true
            ],
            'email' => [
                'type' => 'string',
                'pattern' => '/^\w+@\w+\.[a-zA-Z]{2,}$/',
                'required' => true
            ],
            'subject' => [
                'type' => 'plain_text',
                'required' => true
            ]
        ];
    }

    public function __get($name)
    {
        if (in_array($name, ['name', 'email', 'subject']) && $this->isValid()) {
            return $this->data[$name];
        }
    }

    protected function getFieldName(string $name): string
    {
        return t(self::FIELD_NAMES[$name] ?? $name);
    }

    public function sendAsMail(): void
    {
        $timestamp = time();
        $to = app()->config('webmaster')->mail;
        $subject = 'Сообщение с сайта от пользователя ' . date('m.d H:i', $timestamp);
        $message = 'Сообщение от: ' . $this->name . ' (' . $this->email . ")\r\n";
        $message .= 'Отправлено в: ' . date('Y-m-d H:i:s', $timestamp) . "\r\n";
        $message .= "Текст сообщения:\r\n" . $this->subject;
        $headers = [
            'From' => $this->email,
            'Reply-To' => $this->email,
            'Date' => date('D, d M Y H:i:s O', $timestamp)
        ];
        app()->mailer()->send($to, $subject, $message, $headers);
        return;
    }
}
