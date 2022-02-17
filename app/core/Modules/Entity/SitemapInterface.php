<?php

namespace Blog\Modules\Entity;

interface SitemapInterface
{
    public static function getSitemapChangefreq();
    public static function getSitemapPriority();
}