<?php
namespace Frontend42\Controller\Frontend;

use Core42\Mvc\Controller\AbstractActionController;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\Selector\NavigationSelector;
use Zend\View\Model\ViewModel;

class DefaultController extends AbstractActionController
{
    protected function getViewModel()
    {
        $sitemap = $this->getEvent()->getRouteMatch()->getParam("__sitemap__");

        $pageType = $this->getServiceManager()->get(PageTypePluginManager::class)->get($sitemap->getPageType());

        $viewModel = new ViewModel();
        if (method_exists($pageType, 'getView')) {
            $viewModel->setTemplate($pageType->getView());
        }

        return $viewModel;
    }

    public function localizationAction()
    {
        $this->getSelector(NavigationSelector::class)->setLocale('en-US')->setNavigation('main')->getResult();
        die();
        return $this->getViewModel();
    }

    public function contentAction()
    {
        return $this->getViewModel();
    }
}
