<?php

namespace Blog\Request;

use Blog\Modules\Entity\ArticlePrototype;

class ArticleRequest extends RequestPrototype
{
    protected const ACCESS_LEVEL = 4;
    protected const CSRF_SKIP = true;

    #[RequestPropertyLabelAttribute('Article title')]
    #[Validators\Type('string')]
    #[Validators\StringLength(256)]
    #[Validators\Required(true)]
    protected string $title;
    
    #[RequestPropertyLabelAttribute('Alias path')]
    #[Preproccessors\ArticleAlias]
    #[Validators\Type('string')]
    #[Validators\Pattern('/[\w\-]*/')]
    #[Validators\StringLength(256)]
    protected string $alias;

    #[RequestPropertyLabelAttribute('Image preview src link')]
    #[Preproccessors\DefaultValue(ArticlePrototype::DEFAULT_PREVIEW_SRC)]
    #[Validators\Type('string')]
    #[Validators\StringLength(256)]
    protected string $preview_src;

    #[RequestPropertyLabelAttribute('Image alt text')]
    #[Preproccessors\DefaultValue(ArticlePrototype::DEFAULT_PREVIEW_ALT)]
    #[Validators\Type('string')]
    #[Validators\StringLength(256)]
    protected string $preview_alt;

    #[RequestPropertyLabelAttribute('Summary')]
    #[Validators\Type('string')]
    #[Validators\StringLength(512)]
    #[Validators\Required(true)]
    #[Formatters\PlainText]
    protected string $summary;

    #[RequestPropertyLabelAttribute('Article body')]
    #[Validators\Type('string')]
    #[Validators\Required(true)]
    #[Formatters\HtmlText(
        html_strategy: Formatters\HtmlText::STRATEGY_FULL,
        allow_attributes: true
    )]
    protected string $body;

    #[RequestPropertyLabelAttribute('Author')]
    #[Preproccessors\DefaultValue(ArticlePrototype::DEFAULT_AUTHOR)]
    #[Validators\Type('string')]
    #[Validators\StringLength(48)]
    protected string $author;

    #[RequestPropertyLabelAttribute('Publishing status')]
    #[Preproccessors\DefaultValue(0)]
    #[Validators\Type('int')]
    protected string $status;
}
