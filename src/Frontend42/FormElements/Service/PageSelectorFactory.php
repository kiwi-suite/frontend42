<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\FormElements\Service;

use Core42\I18n\Localization\Localization;
use Frontend42\FormElements\PageSelector;
use Frontend42\Selector\SitemapSelector;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PageSelectorFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sitemapArray = [];

        /** @var Localization $localization */
        $localization = $serviceLocator->getServiceLocator()->get('Localization');
        foreach ($localization->getAvailableLocales() as $_locale) {
            /** @var SitemapSelector $selector */
            $selector = $serviceLocator->getServiceLocator()->get('Selector')->get('Frontend42\Sitemap');

            $sitemapArray[$_locale] = $this->prepareLocaleArray(
                $selector->setIncludeExclude(false)->setLocale($_locale)->getResult()
            );
        }

        $element = new PageSelector();
        $element->setSitemapData($sitemapArray);

        return $element;
    }

    protected function prepareLocaleArray(array $sitemap, $level = 0)
    {
        $preparedArray = [];

        foreach ($sitemap as $_sitemap) {
            $preparedArray[] = [
                'sitemapId' => $_sitemap['sitemap']->getId(),
                'pageId' => $_sitemap['page']->getId(),
                'name' => $_sitemap['page']->getName(),
                'status' => $_sitemap['page']->getStatus(),
                'level' => $level
            ];

            if (!empty($_sitemap['children']) && is_array($_sitemap['children'])) {
                $preparedArray = array_merge(
                    $preparedArray,
                    $this->prepareLocaleArray($_sitemap['children'], $level + 1)
                );
            }
        }

        return $preparedArray;
    }
}
