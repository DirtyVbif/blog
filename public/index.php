<?php

define('ROOTDIR', '../');

require_once ROOTDIR . 'app/autoload.php';

app()->run();

msgr()->warning(
    'Обратите внимание: в данный момент сайт находится в процессе разработки. Если Вы наткнулись на какую-либо ошибку в работе сайта, пожалуйста, @contact_me.',
    markup: ['contact_me' => '<a href="' . tpllink('contacts', '/')->url . '">свяжитесь со мной</a>'],
    class: 'development'
);

echo app();
