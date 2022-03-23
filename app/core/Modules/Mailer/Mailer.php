<?php

namespace Blog\Modules\Mailer;

use Blog\Components\AbstractModule;
use Blog\Components\ModuleInterface;
use Blog\Modules\Entity\Feedback;
use Blog\Client\User;
use Blog\Request\FeedbackRequest;

class Mailer extends AbstractModule implements ModuleInterface
{
    public function send(string $to, string $subject, string $message, array $headers): bool
    {
        if (function_exists('mail')) {
            return mail($to, $subject, $message, $headers);
        }
        msgr()->error('Function `mail` doesn\'t exists or disabled in php.ini.', access_level: User::ACCESS_LEVEL_ADMIN);
        return false;
    }

    public function sendFeedback(FeedbackRequest $request): void
    {
        $timestamp = time();
        $to = app()->config('webmaster')->mail;
        $from = $request->email;
        $subject = 'Сообщение с сайта от пользователя ' . date('m.d H:i', $timestamp);
        $data = [
            'name' => $request->name,
            'email' => $from,
            'date' => date('Y-m-d H:i:s', $timestamp),
            'message' => $request->subject
        ];
        $email = new EMail($to, $from, $subject);
        $email->setData($data);
        $result = $this->send($to, $subject, $email->getHtmlBody(), $email->getHeaders());
        Feedback::create($request, ['email' => $email, 'status' => $result]);
        return;
    }
}
