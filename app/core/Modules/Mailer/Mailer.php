<?php

namespace Blog\Modules\Mailer;

use Blog\Components\AbstractModule;
use Blog\Components\ModuleInterface;
use Blog\Request\FeedbackRequest;

class Mailer extends AbstractModule implements ModuleInterface
{
    public function send(string $to, string $subject, string $message, array $headers = []): void
    {
        $headers['X-Mailer'] = 'PHP/' . phpversion();
        $result = mail($to, $subject, $message, $headers);
        $headers['Timestamp'] = time();
        $this->storeSendMail($to, $subject, $message, $headers, $result);
        return;
    }

    protected function storeSendMail(string $to, string $subject, string $message, array $headers, bool $result): void
    {
        $sql = sql_insert('mailer_sended_mails');
        $sql->set(
            [$subject, $message, json_encode($headers), $headers['Timestamp'], (int)$result],
            ['subject', 'message', 'headers', 'timestamp', 'status']
        );
        $sql->exe();
        return;
    }

    public function sendFeedback(FeedbackRequest $request): void
    {
        $timestamp = time();
        $to = app()->manifest()->webmaster->email;
        $subject = 'Сообщение с сайта от пользователя ' . date('m.d H:i', $timestamp);
        $headers = [
            'From' => $request->email,
            'Reply-To' => $request->email,
            'Date' => date('D, d M Y H:i:s O', $timestamp)
        ];
        app()->twig_add_namespace('app/core/Modules/Mailer/src/', 'mailer');
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'date' => date('Y-m-d H:i:s', $timestamp),
            'message' => $request->subject
        ];
        $message = app()->twig()->render('@mailer/message.html.twig', $data);
        $headers['X-Mailer'] = 'PHP/' . phpversion();
        $result = mail($to, $subject, $message, $headers);
        $headers['Timestamp'] = time();
        $message = "{$data['name']} ({$data['email']}): {$data['message']}";
        $this->storeSendMail($to, $subject, $message, $headers, $result);
        return;
    }
}
