<?php
namespace Frontend42\Controller;

use Admin42\Authentication\AuthenticationService;
use Core42\Navigation\Filter\IsActiveFilter;
use Core42\Navigation\Navigation;
use Core42\View\Model\JsonModel;
use Frontend42\Command\Sitemap\AddSitemapCommand;
use Frontend42\Selector\FlatSelector;
use Zend\View\Model\ViewModel;

abstract class FlatController extends SitemapController
{
    /**
     * @var int
     */
    protected $sitemapId = 0;

    /**
     * @var string
     */
    protected $pageType = "";


    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this
                ->getSelector(FlatSelector::class)
                ->setSitemapId($this->sitemapId)
                ->getResult();
        }

        /** @var Navigation $navigation */
        $navigation = $this->getServiceManager()->get(Navigation::class);
        $filter = new IsActiveFilter($navigation->getContainer('admin42'), $navigation);
        $iterator = new \RecursiveIteratorIterator($filter, \RecursiveIteratorIterator::SELF_FIRST);
        $iterator->setMaxDepth(-1);

        $currentPage = null;
        foreach ($iterator as $page) {
            $currentPage = $page;
        }

        $viewModel = new ViewModel([
            'routes' => $this->getAllRoutes(),
            'label' => $currentPage->getOption('label'),
            'icon' => $currentPage->getOption('icon'),
        ]);
        $viewModel->setTemplate('frontend42/flat/index');

        return $viewModel;
    }

    /**
     * @return \Zend\Http\Response
     */
    public function listAction()
    {
        return $this->redirect()->toRoute($this->getRoute("index"));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function saveAction()
    {
        return $this->redirect()->toRoute($this->getRoute("index"));
    }

    /**
     * @return JsonModel
     * @throws \Exception
     */
    public function addSitemapAction()
    {
        $authenticationService = $this->getServiceManager()->get(AuthenticationService::class);

        $parentPage = $this->getTableGateway('Frontend42\Page')->select([
            'sitemapId' => $this->sitemapId,
            'locale' => $this->params()->fromPost('locale')
        ])->current();

        /* @var AddSitemapCommand $cmd */
        $cmd = $this->getCommand('Frontend42\Sitemap\AddSitemap')
            ->setPageType($this->pageType)
            ->setCreatedUser($authenticationService->getIdentity())
            ->setName($this->params()->fromPost('name'))
            ->setParentPageId($parentPage->getId());

        $page = $cmd->run();

        return new JsonModel([
            'success' => true,
            'url' => $this->url()->fromRoute($this->getRoute("edit"), ['id' => $page->getId()])
        ]);
    }
}
