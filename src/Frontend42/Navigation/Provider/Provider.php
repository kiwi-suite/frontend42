<?php
namespace Frontend42\Navigation\Provider;

use Core42\Navigation\Container;
use Core42\Navigation\Page\PageFactory;
use Core42\Navigation\Provider\AbstractProvider;
use Frontend42\Selector\SitemapSelector;

class Provider extends AbstractProvider
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var SitemapSelector
     */
    protected $sitemapSelector;

    /**
     * @var string
     */
    protected $defaultLocale;

    public function __construct(SitemapSelector $sitemapSelector, $defaultLocale)
    {
        $this->sitemapSelector = $sitemapSelector;
        $this->defaultLocale = $defaultLocale;
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

        $pages = $this->buildNavigation($this->getSitemap($this->defaultLocale), 'frontend');

        foreach ($pages as $page) {
            $this->container->addPage(PageFactory::create($page, $containerName));
        }

        $this->container->sort();
        return $this->container;
    }

    protected function getSitemap($locale)
    {
        return $this->sitemapSelector
            ->setLocale($locale)
            ->setIncludeOffline(false)
            ->setIncludeExclude(false)
            ->getResult();
    }

    /**
     * @param $sitemap
     * @return array
     */
    protected function buildNavigation($sitemap, $prefix)
    {
        $pages = [];

        foreach ($sitemap as $_sitemap) {
            $page = [
                'options' => [
                    'label'     => $_sitemap['page']->getName(),
                    'pageId'    => $_sitemap['page']->getId(),
                    'sitemapId' => $_sitemap['sitemap']->getId(),
                    'order'     => $_sitemap['sitemap']->getOrderNr(),
                    'route'     => $prefix . '/' . $_sitemap['page']->getLocale() . '-' . $_sitemap['sitemap']->getId()
                ]
            ];

            if (!empty($_sitemap['children'])) {
                $page['pages'] = $this->buildNavigation(
                    $_sitemap['children'],
                    $prefix . '/' . $_sitemap['page']->getLocale() . '-' . $_sitemap['sitemap']->getId()
                );
            }

            $pages[] = $page;
        }

        return $pages;
    }
}
