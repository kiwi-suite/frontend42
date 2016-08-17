<?php
namespace Frontend42\Command\Frontend;

use Core42\Command\AbstractCommand;
use Core42\I18n\Localization\Localization;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\Selector\SitemapSelector;

class BuildIndexCommand extends AbstractCommand
{
    /**
     * @var boolean
     */
    protected $force = false;

    protected $routePrefix = "frontend";

    /**
     * @param $force
     * @return $this
     */
    public function setForce($force)
    {
        $this->force = $force;

        return $this;
    }

    /**
     * @param $routePrefix
     * @return $this
     */
    public function setRoutePrefix($routePrefix)
    {
        $this->routePrefix = $routePrefix;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        foreach ($this->getServiceManager()->get(Localization::class)->getAvailableLocales() as $locale) {
            $tree = $this
                ->getSelector(SitemapSelector::class)
                ->setLocale($locale)
                ->getResult();

            $index = $this->buildRecursive($tree, $locale, $this->routePrefix);
            var_dump($index);
        }


        die();
    }

    protected function buildRecursive($tree, $locale, $routePrefix)
    {
        $navigation = [];
        $routing = [];

        foreach ($tree as $item) {
            /** @var Page $page */
            $page = $item['page'];

            /** @var Sitemap $sitemap */
            $sitemap = $item['sitemap'];

            $route = $this->routePrefix . '/' . $page->getId();

            $routing[$route] = $page->getRoute();

            $navPage = [
                'options' => [
                    'label'     => $page->getName(),
                    'pageId'    => $page->getId(),
                    'sitemapId' => $sitemap->getId(),
                    'order'     => $sitemap->getOrderNr(),
                    'route'     => $route
                ]
            ];

            if (!empty($item['children'])) {
                $childrenItems = $this->buildRecursive($item['children'], $locale, $route);

                if (!empty($childrenItems['routing'])) {
                    $routing[$route]['child_routes'] = $childrenItems['routing'];
                }

                if (!empty($childrenItems['navigation'])) {
                    $navPage['pages'] = $childrenItems['navigation'];
                }
            }

            $navigation[] = $navPage;
        }

        return [
            'navigation' => $navigation,
            'routing' => $routing,
        ];
    }
}
