<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Command\Navigation;

class CreateFrontendNavigationCommand extends \Core42\Command\AbstractCommand
{
    use \Core42\Command\ConsoleAwareTrait;

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

        foreach ($locales as $locale) {
            $pages = $this->buildNavigation(
                $sitemapSelector->setLocale($locale)
                    ->setIncludeOffline(false)
                    ->setIncludeExclude(false)
                    ->setIncludeExcludeFromMenu(false)
                    ->getResult(),
                'frontend'
            );

            $cache = $this->getServiceManager()->get('Cache\Sitemap');
            $cache->setItem("nav_" . $locale, $pages);
        }

    }

    /**
     * @param $sitemap
     * @return array
     */
    protected function buildNavigation($sitemap, $prefix)
    {
        $pages = [];

        foreach ($sitemap as $currentSitemap) {
            $page = [
                'options' => [
                    'label'     => $currentSitemap['page']->getName(),
                    'pageId'    => $currentSitemap['page']->getId(),
                    'sitemapId' => $currentSitemap['sitemap']->getId(),
                    'order'     => $currentSitemap['sitemap']->getOrderNr(),
                    'route'     => $prefix . '/' . $currentSitemap['page']->getLocale() . '-' . $currentSitemap['sitemap']->getId()
                ]
            ];

            if (!empty($currentSitemap['children'])) {
                $page['pages'] = $this->buildNavigation(
                    $currentSitemap['children'],
                    $prefix . '/' . $currentSitemap['page']->getLocale() . '-' . $currentSitemap['sitemap']->getId()
                );
            }

            $pages[] = $page;
        }

        return $pages;
    }

    /**
     * @param \ZF\Console\Route $route
     * @return void
     */
    public function consoleSetup(\ZF\Console\Route $route)
    {
    }
}
