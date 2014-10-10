<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Navigation\Provider;

use Core42\Navigation\Container;
use Core42\Navigation\Page\PageFactory;
use Core42\Navigation\Provider\AbstractProvider;
use Frontend42\Model\Page;
use Frontend42\Sitemap\SitemapProvider;
use Frontend42\TableGateway\PageTableGateway;

class DatabaseProvider extends AbstractProvider
{
    /**
     * @var SitemapProvider
     */
    protected $sitemapProvider;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var PageTableGateway
     */
    protected $pageTableGateway;

    /**
     * @param SitemapProvider $sitemapProvider
     * @param PageTableGateway $pageTableGateway
     */
    public function __construct(SitemapProvider $sitemapProvider, PageTableGateway $pageTableGateway)
    {
        $this->sitemapProvider = $sitemapProvider;

        $this->pageTableGateway = $pageTableGateway;
    }

    /**
     * @param string $containerName
     * @return Container
     */
    public function getContainer($containerName)
    {
        if ($this->container instanceof Container) {
            return $this->container;
        }

        $this->container = new Container();
        $this->container->setContainerName($containerName);
/*
        $result = $this->pageTableGateway->select(function(Select $select){
            $select->where
                    ->nest()
                        ->isNull('publishedFrom')
                        ->or
                        ->lessThanOrEqualTo('publishedFrom', date('Y-m-d H:i:s', time()))
                    ->unnest()
                    ->and
                    ->nest()
                        ->isNull('publishedUntil')
                        ->or
                        ->greaterThanOrEqualTo('publishedUntil', date('Y-m-d H:i:s', time()))
                    ->unnest()
                    ->and
                    ->equalTo('status', 'active')
                    ->and
                    ->equalTo('locale', \Locale::getDefault());
        });
*/

        $pages = $this->buildNavigation($this->sitemapProvider->getTreeWithLocale(\Locale::getDefault()));
        foreach ($pages as $page) {
            $this->container->addPage(PageFactory::create($page, $containerName));
        }

        $this->container->sort();
        return $this->container;
    }

    /**
     * @param array $sitemap
     * @param string $routePrefix
     * @return array
     */
    protected function buildNavigation($sitemap, $routePrefix = "")
    {
        $pages = array();

        foreach ($sitemap as $_sitemap) {
            /** @var \Frontend42\Model\Sitemap $sitemapModel */
            $sitemapModel = $_sitemap['model'];

            if (empty($_sitemap['language'])) {
                continue;
            }
            /** @var Page $pageModel */
            $pageModel = $_sitemap['language'];

            if ($pageModel->getStatus() !== Page::STATUS_ACTIVE) {
                continue;
            }

            //TODO published check

            $route = $routePrefix . 'page_' .$sitemapModel->getId();

            $page = array(
                'options' => array(
                    'label' => $pageModel->getTitle(),
                    'route' => $route,
                    'sitemapId' => $sitemapModel->getId(),
                    'metaDescription' => $pageModel->getMetaDescription(),
                    'metaKeywords' => $pageModel->getMetaKeywords(),
                    'order' => $sitemapModel->getOrderNr(),
                ),
            );

            if (!empty($_sitemap['children'])) {

                $page['pages'] = $this->buildNavigation(
                    $_sitemap['children'],
                    $route . '/'
                );
            }

            $pages[$sitemapModel->getId()] = $page;
        }

        return $pages;
    }
}
