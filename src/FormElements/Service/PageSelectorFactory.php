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
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class PageSelectorFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sitemapArray = [];

        /** @var Localization $localization */
        $localization = $container->get(Localization::class);
        foreach ($localization->getAvailableLocales() as $_locale) {
            /** @var SitemapSelector $selector */
            $selector = $container->get('Selector')->get(SitemapSelector::class);

            $sitemapArray[$_locale] = $this->prepareLocaleArray(
                $selector->setIncludeExclude(false)->setLocale($_locale)->getResult()
            );
        }

        $element = new PageSelector();
        $element->setSitemapData($sitemapArray);

        return $element;
    }

    /**
     * @param array $sitemap
     * @param int $level
     * @return array
     */
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
