<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Command\Router;

use Core42\I18n\Localization\Localization;
use Frontend42\Model\Page;

class CreateRouteConfigCommand extends \Core42\Command\AbstractCommand
{
    use \Core42\Command\ConsoleAwareTrait;

    /**
     * @var array
     */
    protected $pageMapping = [];

    /**
     * @var array
     */
    protected $handleMapping = [];

    /**
     * @var array
     */
    protected $localeMapping = [];

    /**
     * @var bool
     */
    protected $caching = true;

    /**
     * @var bool
     */
    protected $includeOffline = false;

    /**
     * @param boolean $caching
     * @return $this
     */
    public function setCaching($caching)
    {
        $this->caching = $caching;

        return $this;
    }

    /**
     * @param $includeOffline
     * @return $this
     */
    public function setIncludeOffline($includeOffline)
    {
        $this->includeOffline = $includeOffline;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function execute()
    {

        /* @var Localization $localisation*/
        $localisation = $this->getServiceManager()->get('Localization');
        $locales = $localisation->getAvailableLocales();

        /* @var \Frontend42\Selector\SitemapSelector $sitemapSelector */
        $sitemapSelector = $this->getServiceManager()->get('Selector')->get('Frontend42\Sitemap');

        $childRoutes = [];
        foreach ($locales as $locale) {

            $sitemapResult = $sitemapSelector
                ->setIncludeExclude(false)
                ->setIncludeOffline($this->includeOffline)
                ->setLocale($locale)
                ->getResult();

            $childRoutes = array_merge($this->buildRoutes($sitemapResult, $locale, 'frontend'), $childRoutes);
        }

        $this->finalize();

        if ($this->caching ===  true) {
            $cache = $this->getServiceManager()->get('Cache\Sitemap');
            $cache->setItem("sitemap", $childRoutes);
            $cache->setItem("pageMapping", $this->pageMapping);
            $cache->setItem("handleMapping", $this->handleMapping);
        }

        return [
            'sitemap' => $childRoutes,
            'pageMapping' => $this->pageMapping,
            'handleMapping' => $this->handleMapping,
        ];
    }

    /**
     * @param array $sitemap
     * @return array
     */
    protected function buildRoutes($sitemap, $locale, $routePrefix)
    {
        $pageRoutes = [];

        foreach ($sitemap as $_sitemap) {
            /* @var Page $page*/
            $page = $_sitemap['page'];
            $pageRoute = json_decode($page->getRoute(), true);

            if (empty($pageRoute)) {
                continue;
            }

            if ($_sitemap['sitemap']->getExclude() === true) {
                continue;
            }

            $pageRoute['options']['defaults']['locale'] = $_sitemap['page']->getLocale();
            $pageRoute['options']['defaults']['pageId'] = $_sitemap['page']->getId();
            $pageRoute['options']['defaults']['sitemapId'] = $_sitemap['sitemap']->getId();

            $key = $locale . '-' . $_sitemap['sitemap']->getId();

            $this->pageMapping[$_sitemap['page']->getId()] = [
                'route'     => $routePrefix . '/' . $key,
                'sitemapId' => $_sitemap['sitemap']->getId(),
            ];
            $this->localeMapping[$_sitemap['sitemap']->getId()][$locale] = $_sitemap['page']->getId();

            if ($_sitemap['sitemap']->getHandle()) {
                $this->handleMapping[$_sitemap['sitemap']->getHandle()][$locale] = $_sitemap['page']->getId();
            }

            if (count($_sitemap['children']) > 0) {
                $tmpRoutes = $this->buildRoutes($_sitemap['children'], $locale, $routePrefix . '/' . $key);

                if (!empty($tmpRoutes)) {
                    $pageRoute['may_terminate'] = true;
                    $pageRoute['child_routes'] = $tmpRoutes;
                }
            }

            $pageRoutes[$key] = $pageRoute;
        }

        return $pageRoutes;
    }

    protected function finalize()
    {
        foreach ($this->pageMapping as $key => $_mapping) {
            unset($this->pageMapping[$key]['sitemapId']);
            $this->pageMapping[$key]['locale'] = $this->localeMapping[$_mapping['sitemapId']];
        }
    }

    /**
     * @param \ZF\Console\Route $route
     * @return void
     */
    public function consoleSetup(\ZF\Console\Route $route)
    {
    }
}
