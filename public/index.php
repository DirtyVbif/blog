<?php

use Blog\Request\RequestFactory;

require_once __DIR__ . '/../vendor/autoload.php';

// Кусок кода для воспроизведения ошибки
// возникающей при вызове метода ReflectionAttribute::newInstance()
$request = RequestFactory::get('login', [
    'mail' => 'admin@mublog.site',
    'password' => 'AfzqufyrX24D',
    'remember_me' => true
]);
try {
    $request->validate();
} catch (\Throwable $th) {
    pre($e);
    exit;
}
pre($request);
exit;
// Далее идёт код идентичный коду /home/d/dirtymike/mublog.site/public_html/index.php
// необходимый для функционирования сайта

app()->run();

msgr()->warning(
    'Обратите внимание: в данный момент сайт находится в процессе разработки. Если Вы наткнулись на какую-либо ошибку в работе сайта, пожалуйста, @contact_me.',
    markup: ['contact_me' => '<a href="' . tpllink('contacts', '/')->url . '">свяжитесь со мной</a>'],
    class: 'development'
);

echo app();
