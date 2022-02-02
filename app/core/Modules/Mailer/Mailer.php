<?php

namespace Blog\Modules\Mailer;

class Mailer
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
            ['subject', 'message', 'headers', 'timestamp', 'result']
        );
        $sql->exe();
        return;
    }
}
