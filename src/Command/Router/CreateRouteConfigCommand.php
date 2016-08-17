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
use Frontend42\Command\Navigation\CreateFrontendNavigationCommand;
use Frontend42\Event\SitemapEvent;
use Frontend42\Model\Page;
use Frontend42\Selector\SitemapSelector;

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
        $sitemapSelector = $this->getServiceManager()->get('Selector')->get(SitemapSelector::class);

        $childRoutes = [];
        foreach ($locales as $locale) {

            $sitemapResult = $sitemapSelector
                //->setIncludeExclude(false)
                //->setIncludeOffline($this->includeOffline)
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
            $cache->setItem("sitemapMapping", $this->localeMapping);
        }

        $this->getCommand(CreateFrontendNavigationCommand::class)->run();

        $this
            ->getServiceManager()
            ->get('Frontend42\Sitemap\EventManager')
            ->trigger(SitemapEvent::EVENT_GENERATE_SITEMAP);

        return [
            'sitemap' => $childRoutes,
            'pageMapping' => $this->pageMapping,
            'handleMapping' => $this->handleMapping,
            'sitemapMapping' => $this->localeMapping,
        ];
    }

    /**
     * @param array $sitemap
     * @return array
     */
    protected function buildRoutes($sitemap, $locale, $routePrefix)
    {
        $pageRoutes = [];

        foreach ($sitemap as $currentSitemap) {
            /* @var Page $page*/
            $page = $currentSitemap['page'];
            $pageRoute = $page->getRoute();

            if (empty($pageRoute)) {
                continue;
            }

            if ($currentSitemap['sitemap']->getExclude() === true) {
                continue;
            }

            $pageRoute['options']['defaults']['locale'] = $currentSitemap['page']->getLocale();
            $pageRoute['options']['defaults']['pageId'] = $currentSitemap['page']->getId();
            $pageRoute['options']['defaults']['sitemapId'] = $currentSitemap['sitemap']->getId();

            $key = $locale . '-' . $currentSitemap['sitemap']->getId();

            $this->pageMapping[$currentSitemap['page']->getId()] = [
                'route'     => $routePrefix . '/' . $key,
                'sitemapId' => $currentSitemap['sitemap']->getId(),
            ];
            $this->localeMapping[$currentSitemap['sitemap']->getId()][$locale] = $currentSitemap['page']->getId();

            if ($currentSitemap['sitemap']->getHandle()) {
                $this->handleMapping[$currentSitemap['sitemap']->getHandle()][$locale] = $currentSitemap['page']->getId();
            }

            if (count($currentSitemap['children']) > 0) {
                $tmpRoutes = $this->buildRoutes($currentSitemap['children'], $locale, $routePrefix . '/' . $key);

                if (!empty($tmpRoutes)) {
                    $pageRoute['may_terminate'] = true;
                    $pageRoute['child_routes'] = $tmpRoutes;
                }
            }

            $pageRoutes[$key] = $pageRoute;
        }

        return $pageRoutes;
    }

    /**
     *
     */
    protected function finalize()
    {
        foreach ($this->pageMapping as $key => $mapping) {
            unset($this->pageMapping[$key]['sitemapId']);
            $this->pageMapping[$key]['locale'] = $this->localeMapping[$mapping['sitemapId']];
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
