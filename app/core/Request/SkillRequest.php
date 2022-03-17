<?php

namespace Blog\Request;

class SkillRequest extends RequestPrototype
{
    protected const ACCESS_LEVEL = 4;
    protected const CSRF_SKIP = true;

    #[RequestPropertyLabelAttribute('Skill title')]
    #[Validators\Type('string')]
    #[Validators\StringLength(256)]
    #[Validators\Required(true)]
    protected string $title;

    #[RequestPropertyLabelAttribute('Skill icon src link')]
    #[Validators\Type('string')]
    #[Validators\StringLength(256)]
    #[Validators\Required(true)]
    protected string $icon_src;

    #[RequestPropertyLabelAttribute('Skill icon alt text')]
    #[Validators\Type('string')]
    #[Validators\StringLength(256)]
    #[Validators\Required(true)]
    protected string $icon_alt;

    #[RequestPropertyLabelAttribute('Message')]
    #[Validators\Type('string')]
    #[Validators\Required(true)]
    #[Formatters\HtmlText(Formatters\HtmlText::STRATEGY_FULL, true)]
    protected string $body;
}
