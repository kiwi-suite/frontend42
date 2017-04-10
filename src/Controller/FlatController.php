<?php
namespace Frontend42\Controller;

use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\I18n\Localization\Localization;
use Core42\Navigation\Service\NavigationPluginManager;
use Frontend42\Selector\SmartTable\FlatSitemapSelector;
use Frontend42\TableGateway\SitemapTableGateway;

class FlatController extends AbstractAdminController
{
    public function indexAction()
    {
        $handle = $this->params()->fromRoute("handle");
        if (empty($handle)) {
            throw new \Exception("invalid handle");
        }

        $sitemapResult = $this
            ->getTableGateway(SitemapTableGateway::class)
            ->select(['handle' => $handle]);

        if ($sitemapResult->count() === 0) {
            throw new \Exception(sprintf("invalid handle '%s'", $handle));
        }
        $sitemap = $sitemapResult->current();

        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->getSelector(FlatSitemapSelector::class)->setSitemap($sitemap)->getResult();
        }

        $pageIcon = "";
        $pageName = "";
        $navigation = $this->getServiceManager()->get(NavigationPluginManager::class)->build('admin42');
        $navigation = new \RecursiveIteratorIterator($navigation, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($navigation as $page) {
            if ($page->getRoute() !== "admin/flat-sitemap") {
                continue;
            }

            if (!$page->isActive()) {
                continue;
            }

            $pageIcon = $page->getIcon();
            $pageName = $page->getLabel();
            break;
        }

        $localization = $this->getServiceManager()->get(Localization::class);
        $defaultLocale = $localization->getDefaultLocale();
        $availableLocales = [];
        foreach ($localization->getAvailableLocalesDisplay() as $locale => $localeDisplay) {
            //TODO permission check

            $availableLocales[$locale] = $localeDisplay;
        }

        if (!in_array($defaultLocale, array_keys($availableLocales))) {
            $defaultLocale = key($availableLocales);
            reset($availableLocales);
        }

        return [
            'pageIcon' => $pageIcon,
            'pageName' => $pageName,
            'defaultLocale' => $defaultLocale,
            'availableLocales' => $availableLocales,
            'sitemap' => $sitemap,
        ];
    }
}
