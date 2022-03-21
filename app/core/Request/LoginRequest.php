<?php

namespace Blog\Request;

class LoginRequest extends RequestPrototype
{
    protected const ACCESS_LEVEL = 1;

    // TODO: set single attribute for validating email
    #[RequestPropertyLabelAttribute('Login')]
    #[Validators\Type('string')]
    #[Validators\Pattern(self::EMAIL_PATTERN)]
    #[Validators\Required(true)]
    protected string $mail;
    
    #[RequestPropertyLabelAttribute('Password')]
    #[Validators\Type('string')]
    #[Validators\StringLength(64, 8)]
    #[Validators\Pattern("/[\w\@\%\#\!\?\&\$\-]{8,64}/")]
    #[Validators\Required(true)]
    protected string $password;

    #[RequestPropertyLabelAttribute('Remember login session')]
    #[Validators\Type('bool')]
    protected bool $remember_me;
}
