<?php

namespace Blog\Modules\Sitemap;

use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\Entity\BlogArticle;
use Blog\Modules\View\Blog;

class Sitemap
{
    protected const SRCPATH = 'app/core/Modules/Sitemap/src/';

    public static function generate(): void
    {
        $links = [];
        // generate main links data
        foreach (app()->builder()->getContent('routes') as $link) {
            if ($link['sitemap_exclude'] ?? false) {
                continue;
            }
            $url = $link['url'];
            $links[$url] = [
                'loc' => fullUrlTo($url),
                'priority' => $link['sitemap_priority'] ?? null,
                'changefreq' => $link['sitemap_changefreq'] ?? null
            ];
        }
        $update = \Blog\Modules\View\Blog::lastUpdate();
        $links['/']['lastmod'] = $links['/blog']['lastmod'] = new DateFormat($update, DateFormat::COMPLETE);
        // generate blog articles data
        foreach (Blog::loadArticlesData() as $article) {
            $url = '/blog/' . $article['alias'];
            $links[$url] = [
                'loc' => fullUrlTo($url),
                'priority' => BlogArticle::getSitemapPriority(),
                'changefreq' => BlogArticle::getSitemapChangefreq(),
                'lastmod' => new DateFormat($article['updated'], DateFormat::COMPLETE)
            ];
        }
        // generate sitemap.xml content from template
        $loader = new \Twig\Loader\FilesystemLoader(ROOTDIR . self::SRCPATH);
        $twig = new \Twig\Environment($loader, [
            'cache' => false,
        ]);
        foreach ($links as $i => $link) {
            if (isset($link['priority']) && empty($link['priority'])) {
                unset($links[$i]);
            } else if (!isset($link['priority'])) {
                continue;
            }
            $links[$i]['priority'] = number_format($link['priority'], 1);
        }
        $sitemap_content = $twig->render('sitemap.html.twig', ['items' => $links]);
        $sitemap_content = preg_replace('/[\r\n\t]+|\>\s+\</', '><', $sitemap_content);
        f('sitemap', '.', 'xml')->addContent($sitemap_content)->save();
        return;
    }
}
