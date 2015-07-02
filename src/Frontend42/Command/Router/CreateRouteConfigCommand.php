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

            $sitemapSelector->setLocale($locale);
            $sitemapResult = $sitemapSelector->getResult();

            foreach ($sitemapResult as $sitemap) {

                $key = $locale . '-' . $sitemap['sitemap']->getId();
                $tmpRoutes = $this->buildRoutes($sitemap, $locale);

                if (!empty($tmpRoutes)) $childRoutes[$key] = $tmpRoutes;
            }
        }

        file_put_contents('data/routing/frontend.php', "<?php\n return " . var_export($childRoutes, true) . ";");
    }

    /**
     * @param array $sitemap
     * @return array
     */
    protected function buildRoutes($sitemap, $locale)
    {
        $childRoutes = [];

        /* @var Page $page*/
        $page = $sitemap['page'];
        $pageRoute = json_decode($page->getRoute(), true);

        if (empty($pageRoute)) {
            return;
        }

        if (count($sitemap['children']) > 0) {
            foreach ($sitemap['children'] as $child) {

                $key = $locale . '-' . $child['sitemap']->getId();

                $tmpRoutes = $this->buildRoutes($child, $locale);

                if (!empty($tmpRoutes)) $childRoutes[$key] = $tmpRoutes;
            }
        }

        $pageRoute['may_terminate'] = true;
        $pageRoute['child_routes'] = $childRoutes;

        return $pageRoute;
    }

    /**
     * @param \ZF\Console\Route $route
     * @return void
     */
    public function consoleSetup(\ZF\Console\Route $route)
    {
    }
}
