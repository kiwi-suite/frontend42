<?php
namespace Frontend42\PageType;

use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;
use Zend\Router\Http\Literal;

class DefaultPageType extends AbstractPageType
{
    /**
     * @param Page $page
     * @param PageContent $pageContent
     * @param Sitemap $sitemap
     * @return array
     */
    public function getRouting(Page $page, PageContent $pageContent, Sitemap $sitemap)
    {
        return [
            'type' => Literal::class,
            'options' => [
                'route' => $page->getSlug().'/',
                'defaults' => [
                    'controller' => $this->getController(),
                    'action' => $this->getAction(),
                    'pageId' => $page->getId(),
                    'locale' => $page->getLocale(),
                ]
            ]
        ];
    }
}
