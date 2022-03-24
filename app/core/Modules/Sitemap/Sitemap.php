<?php

namespace Blog\Modules\Sitemap;

use Blog\Modules\DateFormat\DateFormat;
use Blog\Modules\Entity\Article;

class Sitemap
{
    protected static function getSelfPath(): string
    {
        $class = parseClassname(static::class);
        $path = COREDIR . $class->namespace;
        ffpath($path);
        return $path;
    }

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
        foreach (Article::loadList(['load_with_comments' => false]) as $article) {
            if ($article->exists()) {
                $url = $article->url();
                $links[$url] = [
                    'loc' => fullUrlTo($url),
                    'priority' => Article::getSitemapPriority(),
                    'changefreq' => Article::getSitemapChangefreq(),
                    'lastmod' => new DateFormat($article->get('updated'), DateFormat::COMPLETE)
                ];
            }
        }
        // stimemap source template directory
        $directory = self::getSelfPath() . 'src/';
        // generate sitemap.xml content from template
        $loader = new \Twig\Loader\FilesystemLoader($directory);
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
        // generate sitemap file content from template
        $sitemap_content = $twig->render('sitemap.html.twig', ['items' => $links]);
        // minify sitemap file content
        $sitemap_content = preg_replace(
            ['/[\r\n\t]+/', '/\>\s+/', '/\s+\</'],
            ['', '>', '<'],
            $sitemap_content
        );
        // save sitemap.xml file
        f('sitemap', PUBDIR, 'xml')->addContent($sitemap_content)->save();
        return;
    }
}
