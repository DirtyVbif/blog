<?php

namespace BlogLibrary\HtmlTagsAutofill;

use Blog\Modules\Template\Element;

class HtmlTagsAutofill extends \Blog\Modules\Library\AbstractLibrary
{
    protected const SRC_PATH = 'libraries/HtmlTagsAutofill/src/';
    protected const TWIG_NAMESPACE = 'lib_htmltagsautofill';
    protected const SRC = [
        'js' => [
            'stack' => [
                'js/HtmlTagsAutofill.min.js',
                'js/script.min.js'
            ],
            'public' => 'js/html-tags-autofill.min.js'
        ],
        'css' => [
            'stack' => [
                'css/styles.min.css'
            ],
            'public' => 'css/html-tags-autofill.min.css'
        ]
    ];

    protected static $counter = 0;

    protected bool $twig_namespace_defined = false;
    protected array $tags;

    public function __construct()
    {
        $tags_list = f('tags-list', ROOTDIR . self::SRC_PATH, 'json')
            ->json_decode();
        $this->setTags($tags_list);
    }

    public function use(): void
    {
        $this->checkPublicSources();
        app()->page()->useJs(self::SRC['js']['public']);
        app()->page()->useCss(self::SRC['css']['public']);
    }

    protected function getSources(): object
    {
        return (object)self::SRC;
    }

    public function setTags(array $tags_list): self
    {
        $this->tags = $tags_list;
        return $this;
    }

    public function getTemplate(string $target_element_id): Element
    {
        if (!$this->twig_namespace_defined) {
            app()->twig_add_namespace(self::SRC_PATH, self::TWIG_NAMESPACE);
            $this->twig_namespace_defined = true;
        }
        $id = $target_element_id . "-html-tags-autofill";
        $template = new Element('ul');
        $template->setNamespace(self::TWIG_NAMESPACE);
        $template->setName('tags-list');
        $template->setId($id);
        $template->set('items', $this->tags);
        $template->addClass('js-html-tags-autofill');
        $template->setAttr('data-target-id', $target_element_id);
        self::$counter++;
        return $template;
    }
}