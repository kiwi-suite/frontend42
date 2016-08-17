<?php
namespace Frontend42\PageType;

use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;
use Zend\Router\Http\Segment;

class HomePageType extends AbstractPageType
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
            'type' => Segment::class,
            'options' => [
                'route' => '[/]',
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
