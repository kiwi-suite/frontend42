<?php
namespace Frontend42\Controller;

use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Provider\PageTypeProvider;
use Zend\View\Model\ViewModel;

class ContentController extends AbstractFrontendController
{

    public function indexAction()
    {
        $sitemap = $this->page()->getSitemap();
        /** @var PageTypeInterface $pageType */
        $pageType = $this->getServiceManager()->get(PageTypeProvider::class)->get($sitemap->getPageType());

        $options = $pageType->getOptions();
        if (empty($options['view'])) {
            throw new \Exception("'view' not set in pageType options");
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate($options['view']);
        return $viewModel;
    }
}
