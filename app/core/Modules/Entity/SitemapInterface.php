<?php

namespace Blog\Modules\Entity;

interface SitemapInterface
{
    public static function getSitemapChangefreq(): string;
    public static function getSitemapPriority(): float;
}