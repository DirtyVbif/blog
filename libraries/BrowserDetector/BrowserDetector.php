<?php

namespace BlogLibrary;

class BrowserDetector extends \Blog\Modules\Library\AbstractLibrary
{
    protected const SRC = [
        'js' => [
            'BrowserDetector.min.js',
            'script.min.js'
        ]
    ];
    protected const PUB_NAME = [
        'js' => 'js/browsdetect.min.js'
    ];

    protected string $src_js_content;
    protected string $src_css_content;
    protected array $src_content;

    public function use(): void
    {
        $parser = \UAParser\Parser::create();
        $result = $parser->parse($_SERVER['HTTP_USER_AGENT']);
        pre($result);
        // if (!$this->checkPublicSrcJs()) {
        //     $this->makePublicSrcJs();
        // }
        // app()->page()->useJs(self::PUB_NAME['js']);
        // pre($_SERVER['HTTP_USER_AGENT']);
        return;
    }

    protected function checkPublicSrcJs(): bool
    {
        if (!file_exists(self::PUB_NAME['js'])) {
            return false;
        }
        $pub_content = file_get_contents(self::PUB_NAME['js']);
        return hash_equals(
            md5($this->getSrcContent('js')),
            md5($pub_content)
        );
    }

    protected function makePublicSrcJs(): void
    {
        f(self::PUB_NAME['js'])
            ->content($this->getSrcContent('js'))
            ->save();
        return;
    }

    protected function getSrcListByKey(string $source_key): array
    {
        return self::SRC[$source_key];
    }
}
