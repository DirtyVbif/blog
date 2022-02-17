<?php

namespace Blog\Modules\PageBuilder\Components;

use Blog\Modules\Template\Element;
use Blog\Modules\TemplateFacade\BlockList;
use Blog\Modules\TemplateFacade\BodyText;
use Blog\Modules\TemplateFacade\Form;
use Blog\Modules\TemplateFacade\Image;
use Blog\Modules\TemplateFacade\Title;

trait PageBuilderElements
{
    protected Element $page_header;
    protected Element $page_footer;

    public function header(): Element
    {
        if (!isset($this->page_header)) {
            $this->page_header = new Element;
            $this->page_header->setName('page--header');
            $logo = $this->getLogo();
            $logo->setAttr('class', 'logo_header');
            $this->page_header->set('logo', $logo);
            $this->page_header->set(
                'menu',
                app()->builder()->getMenu('main')
            );
        }
        return $this->page_header;
    }

    public function footer(): Element
    {
        if (!isset($this->page_footer)) {
            $this->page_footer = new Element;
            $this->page_footer->setName('page--footer');
            $logo = $this->getLogo();
            $logo->addClass('logo_footer');
            $this->page_footer->set(
                'menu_footer',
                app()->builder()->getMenu('footer')
            );
            $this->page_footer->set(
                'menu_info',
                app()->builder()->getMenu('info')
            );
            $this->page_footer->set('logo', $logo);
            $this->page_footer->set('copyrights', $this->getCopyrights());
        }
        return $this->page_footer;
    }

    public function getLogo(): Element
    {
        $logo = new Element('a');
        $logo->setName('elements/logo');
        $logo->setAttr('href', '/')
            ->setAttr('title', t('Go home page'))
            ->setAttr('class', 'logo');
        return $logo;
    }

    public function getCopyrights(): Element
    {
        $cp = new Element;
        $cp->setName('elements/copyrights');
        $cp->setAttr('class', 'copyrights');
        $cp->set('current_year', date('Y'));
        return $cp;
    }

    public function getBannerBlock(): Element
    {
        $block = new Element('section');
        $block->setName('blocks/banner');
        $block->addClass('section section_banner');
        return $block;
    }

    public function getSkillsBlock(): Element
    {
        $block = new Element('section');
        $block->setName('blocks/skills');
        $block->addClass('section section_skills')
            ->setId('about');
        $items = $this->getContent('skills');
        foreach ($items as &$item) {
            $item['icon'] = new Image($item['icon']);
            $item['icon']->width(120);
            $item['desc'] = new BodyText($item['desc']);
        }
        $block->set('items', $items);
        $label = new Title(2);
        $label->set(t('My skills'));
        $label->addClass('section__header section_skills__header');
        $block->set('label', $label);
        return $block;
    }

    public function getSummaryBlock(): Element
    {
        $block = new Element('section');
        $block->setName('blocks/summary');
        $block->addClass('section section_summary')
            ->setId('summary');
        $label = new Title(2);
        $label->set(t('Web-developer summary'));
        $label->addClass('section__header section_summary__header');
        $diff = date_diff(
            date_create('26-08-1989'),
            date_create(date("Y-m-d"))
        );
        $block->set('label', $label)
            ->set('my_age', $diff->format('%y'));
        return $block;
    }

    public function getContactsBlock(): Element
    {
        $block = new Element('section');
        $block->setName('blocks/contacts');
        $block->addClass('section section_contacts')
            ->setId('contacts');
        $label = new Title(2);
        $label->set(t('contacts'));
        $label->addClass('section__header section_contacts__header');
        $block->set('label', $label);
        $form = new Form('feedback');
        $block->set('form', $form);
        return $block;
    }

    public function getBlogPreview(): Element
    {
        $block = new Element('section');
        $block->setName('blocks/blog--preview');
        $block->addClass('section section_blog')
            ->setId('blog');
        $label = new Title(2);
        $label->set(t('last posts in blog'));
        $label->addClass('section__header section_blog__header');
        $block->set('label', $label);
        /** @var Blog\Modules\View\Blog $view */
        $view = app()->view('blog');
        $block->set('items', $view->preview(3, 'teaser'));
        return $block;
    }

    public function getBlogPage(): Element
    {
        $block = new Element;
        $block->setName('blocks/blog--page');
        /** @var Blog\Modules\View\Blog $view */
        $view = app()->view('blog')->view();
        $block->set('items', $view->items);
        $block->set('pager', $view->pager);
        return $block;
    }

    public function getBlogCommentsPage(): Element
    {
        $block = new Element;
        $block->setName('blocks/blog--comments');
        /** @var Blog\Modules\View\BlogComments $view */
        $view = app()->view('blog comments')->view();
        $block->set('items', $view->items);
        $block->set('pager', $view->pager);
        return $block;
    }

    public function getCookieModal(): Element
    {
        $chunk = new Element;
        $chunk->setName('elements/accept-cookies');
        $chunk->addClass('cookie-agreement hidden');
        $chunk->setId('cookie-agreement');
        return $chunk;
    }

    public function getLoginForm(): Element
    {
        $form = new Element;
        $form->setName('forms/login');
        return $form;
    }

    public function getUserSessions(): Element
    {
        $block = new Element;
        $block->setName('blocks/user-sessions');
        $block->set('items', user()->getOpenedSessions());
        return $block;
    }
}
