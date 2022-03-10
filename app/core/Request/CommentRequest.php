<?php

namespace Blog\Request;

class CommentRequest extends RequestPrototype
{
    #[RequestPropertyLabelAttribute('Name')]
    #[Validators\Type('string')]
    #[Validators\StringLength(60)]
    #[Validators\Required(true)]
    protected string $name;
    
    #[RequestPropertyLabelAttribute('E-mail')]
    #[Validators\Type('string')]
    #[Validators\Pattern('/^[\w\-\.]+@[\w\-^_]+\.[a-z]{2,}$/i')]
    #[Validators\Required(true)]
    protected string $email;

    #[RequestPropertyLabelAttribute('Comment')]
    #[Validators\Type('string')]
    #[Validators\Required(true)]
    #[Formatters\HtmlText(Formatters\HtmlText::STRATEGY_BASIC)]
    protected string $subject;

    #[RequestPropertyLabelAttribute('Parent comment id')]
    #[Validators\Type('int')]
    protected string $parent_id;

    #[RequestPropertyLabelAttribute('Entity id')]
    #[Validators\Type('int')]
    #[Validators\Required(true)]
    protected string $entity_id;
}
