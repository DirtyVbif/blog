<?php

namespace Blog\Request;

class FeedbackRequest extends RequestPrototype
{
    protected const CSRF_SKIP = true;

    #[RequestPropertyLabelAttribute('Name')]
    #[Validators\Type('string')]
    #[Validators\StringLength(60)]
    #[Validators\Required(true)]
    protected string $name;

    // TODO: set single attribute for validating email
    #[RequestPropertyLabelAttribute('E-mail')]
    #[Validators\Type('string')]
    #[Validators\Pattern(self::EMAIL_PATTERN)]
    #[Validators\Required(true)]
    protected string $email;

    #[RequestPropertyLabelAttribute('Message')]
    #[Validators\Type('string')]
    #[Validators\Required(true)]
    #[Formatters\HtmlText(Formatters\HtmlText::STRATEGY_BASIC)]
    protected string $subject;
}
