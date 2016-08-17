<?php
namespace Frontend42\Controller;

use Admin42\Authentication\AuthenticationService;
use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\View\Model\JsonModel;
use Frontend42\Command\Frontend\BuildIndexCommand;
use Frontend42\Command\Page\EditCommand;
use Frontend42\Command\PageVersion\ApproveCommand;
use Frontend42\Command\Sitemap\AddSitemapCommand;
use Frontend42\Command\Sitemap\ChangePageTypeCommand;
use Frontend42\Command\Sitemap\DeleteSitemapCommand;
use Frontend42\Command\Sitemap\SavePageSortingCommand;
use Frontend42\Form\Sitemap\ChangePageTypeForm;
use Frontend42\Form\Sitemap\CreateForm;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\Provider\PageTypeProvider;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\Selector\SitemapSelector;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\PageVersionTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Select;
use Zend\Form\Form;
use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Router\RouteMatch;
use Zend\View\Model\ViewModel;

class SitemapController extends AbstractAdminController
{
    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        var_dump($this->getCommand(BuildIndexCommand::class)->run());
        die();

        $viewModel = new ViewModel([
            'createForm' => $this->getForm(CreateForm::class),
            'routes' => $this->getAllRoutes(),
        ]);
        $viewModel->setTemplate('frontend42/sitemap/index');

        return $viewModel;
    }

    /**
     * @return JsonModel
     */
    public function listAction()
    {
        $jsonString = $this->getRequest()->getContent();
        $options = Json::decode($jsonString, Json::TYPE_ARRAY);

        $result = $this->getSelector(SitemapSelector::class)
            ->setLocale($options['locale'])
            ->setAuthorizationCheck(true)
            ->setIncludeExclude(false)
            ->getResult();

        return new JsonModel($this->prepareJsonTree($result));
    }

    /**
     * @param $items
     * @return array
     */
    protected function prepareJsonTree($items)
    {
        $tree = [];
        foreach ($items as $_item) {
            $title = $_item['page']->getName();
            $alternateNames = [];
            if (empty($title)) {
                $result = $this
                    ->getTableGateway(PageTableGateway::class)
                    ->select([
                        'sitemapId' => $_item['sitemap']->getId()
                    ]);
                foreach ($result as $_page) {
                    if (strlen($_page->getName()) == 0) {
                        continue;
                    }
                    $alternateNames[] = [
                        'locale' => $_page->getLocale(),
                        'region' => strtolower(\Locale::getRegion($_page->getLocale())),
                        'title'  => $_page->getName()
                    ];
                }
            }
            $node = [
                'id'        => $_item['sitemap']->getId(),
                'pageId'    => $_item['page']->getId(),
                'locale'    => $_item['page']->getLocale(),
                'title'     => $title,
                'status'    => $_item['page']->getStatus(),
                'viewCount' => $_item['page']->getViewCount(),
                'pageType'  => $_item['sitemap']->getPageType(),
                'droppable' => !$_item['sitemap']->getTerminal(),
                'alternateNames' => $alternateNames,
                'items'     => [],
            ];
            if (!empty($_item['children']) && !$_item['sitemap']->getTerminal()) {
                $node['items'] = $this->prepareJsonTree($_item['children']);
            }

            $tree[] = $node;
        }

        return $tree;
    }

    /**
     * @return JsonModel
     */
    public function deleteAction()
    {
        if ($this->getRequest()->isDelete()) {
            $deleteCmd = $this->getCommand(DeleteSitemapCommand::class);

            $deleteParams = array();
            parse_str($this->getRequest()->getContent(), $deleteParams);

            $deleteCmd->setSitemapId((int) $deleteParams['id'])
                ->run();

            return new JsonModel(array(
                'success' => true,
            ));
        } elseif ($this->getRequest()->isPost()) {
            $deleteCmd = $this->getCommand(DeleteSitemapCommand::class);

            $deleteCmd->setSitemapId((int) $this->params()->fromPost('id'))
                ->run();

            $this->flashMessenger()->addSuccessMessage([
                'title' => 'toaster.sitemap.delete.title.success',
                'message' => 'toaster.sitemap.delete.message.success',
            ]);

            return new JsonModel([
                'redirect' => $this->url()->fromRoute($this->getRoute("index"))
            ]);
        }

        return new JsonModel([
            'redirect' => $this->url()->fromRoute($this->getRoute("index"))
        ]);
    }

    /**
     * @return JsonModel
     */
    public function saveAction()
    {
        $this->getCommand(SavePageSortingCommand::class)
            ->setJsonTreeString($this->getRequest()->getContent())
            ->run();

        return new JsonModel(['success' => true]);
    }

    /**
     * @return JsonModel
     * @throws \Exception
     */
    public function addSitemapAction()
    {
        /* @var AddSitemapCommand $cmd */
        $cmd = $this->getCommand(AddSitemapCommand::class)
            ->setPageType($this->params()->fromPost('page_type_selector'))
            ->setCreatedUser($this->getIdentity())
            ->setName($this->params()->fromPost('name'))
            ->setParentPageId($this->params()->fromPost('page_selector'));

        $page = $cmd->run();

        return new JsonModel([
            'success' => true,
            'url' => $this->url()->fromRoute($this->getRoute("edit"), ['id' => $page->getId()])
        ]);
    }

    /**
     * @return ViewModel|\Zend\Http\Response
     * @throws \Exception
     */
    public function editAction()
    {
        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        /** @var PageTypeProvider $pageTypeProvider */
        $pageTypeProvider = $this->getServiceManager()->get(PageTypeProvider::class);

        /** @var Page page */
        $page = $this->getTableGateway(PageTableGateway::class)->selectByPrimary((int) $this->params('id'));

        /** @var Sitemap $sitemap */
        $sitemap = $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary($page->getSitemapId());

        /** @var Form $pageForm */
        $pageForm = $pageTypeProvider->get($sitemap->getPageType())->getPageForm();

        if ($prg !== false) {
            $pageForm->setData($prg);

            if ($pageForm->isValid()) {
                $approve = false;
                if (array_key_exists('save', $prg)) {
                    if ($prg['save'] == 'approve') {
                        $approve = true;
                    }
                    unset($prg['save']);
                }

                $pageContent = $pageTypeProvider->get($sitemap->getPageType())->getPageContent();
                $pageContent->setFromFormData($pageForm->getInputFilter()->getValues());

                /** @var EditCommand $cmd */
                $cmd = $this->getCommand(EditCommand::class);
                $cmd->setPage($page)
                    ->setApprove($approve)
                    ->setUpdateUser($this->getIdentity())
                    ->setPageContent($pageContent);

                $pageVersion = $cmd->run();
                $this->flashMessenger()->addSuccessMessage([
                    'title' => 'Page saved',
                    'message' => 'Page successfully saved',
                ]);

                return $this->redirect()->toRoute($this->getRoute("edit"), ['id' => $pageVersion->getPageId()]);
            } else {
                /** @var PageVersionSelector $selector */
                $selector = $this->getSelector(PageVersionSelector::class)->setPageId($page->getId());
                if ($this->params()->fromRoute('version') !== null) {
                    $selector->setVersionName($this->params()->fromRoute("version"));
                }
                $pageVersion = $selector->getResult();
            }
        } else {
            /** @var PageVersionSelector $selector */
            $selector = $this->getSelector(PageVersionSelector::class)->setPageId($page->getId());
            if ($this->params()->fromRoute('version') !== null) {
                $selector->setVersionName($this->params()->fromRoute("version"));
            }
            $pageVersion = $selector->getResult();
            $pageContent = $pageTypeProvider->get($sitemap->getPageType())->getPageContent();
            $pageContent->setContent($pageVersion->getContent());
            $pageForm->setData($pageContent->generateFormData());
        }

        $versions = $this->getTableGateway(PageVersionTableGateway::class)->select(function (Select $select) use ($page) {
            $select->where(['pageId' => $page->getId()]);
            $select->order('created DESC');
            $select->limit(15);
        });
        $versions->buffer();

        $viewModel = new ViewModel([
            'pageForm' => $pageForm,
            'versions' => $versions,
            'currentVersion' => $pageVersion,
            'page'     => $page,
            'changePageTypeForm' => $this->getForm(ChangePageTypeForm::class),
            'routes' => $this->getAllRoutes(),
        ]);
        $viewModel->setTemplate("frontend42/sitemap/edit");

        return $viewModel;
    }

    /**
     * @return \Zend\Http\Response
     * @throws \Exception
     */
    public function approveAction()
    {
        $pageId = $this->params('id');
        $pageVersionId = $this->params('version');

        $authenticationService = $this->getServiceManager()->get(AuthenticationService::class);

        /* @var ApproveCommand $cmd*/
        $cmd = $this->getCommand(ApproveCommand::class);
        $cmd->setPageId($pageId)
            ->setPageVersionId($pageVersionId)
            ->setUpdatedUser($authenticationService->getIdentity())
            ->run();

        return $this->redirect()->toRoute($this->getRoute("edit"), ['id' => $pageId, 'version' => $pageVersionId]);
    }

    /**
     * @return \Zend\Http\Response
     */
    public function changeLanguageAction()
    {
        $result = $this->getTableGateway(PageTableGateway::class)->select([
            'locale' => $this->params("locale"),
            'sitemapId' => $this->params('sitemapId')
        ]);

        if ($result->count() > 0) {
            return $this->redirect()->toRoute($this->getRoute("edit"), ['id' => $result->current()->getId()]);
        }

        return $this->redirect()->toRoute($this->getRoute("index"));
    }

    /**
     * @return JsonModel
     */
    public function changePageTypeAction()
    {
        $authenticationService = $this->getServiceManager()->get(AuthenticationService::class);
        
        $this->getCommand(ChangePageTypeCommand::class)
            ->setPageType($this->params()->fromPost("page_type"))
            ->setSitemapId($this->params("sitemapId"))
            ->setCreatedBy($authenticationService->getIdentity()->getId())
            ->run();

        return new JsonModel([
            'redirect' => $this->url()->fromRoute($this->getRoute("edit"), ['id' => $this->params('pageId')])
        ]);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getRoute($name)
    {
        /** @var RouteMatch $routeMatch */
        $routeMatch = $this
            ->getServiceManager()
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        $baseRouteName = $routeMatch->getMatchedRouteName();
        if ($routeMatch->getParam("action") != "index") {
            $baseRouteName = substr($baseRouteName, 0, strrpos($baseRouteName, '/'));
        }

        if ($name == "index") {
            return $baseRouteName;
        }

        return $baseRouteName . "/" . $name;
    }

    /**
     * @return array
     */
    protected function getAllRoutes()
    {
        return [
            'index' => $this->getRoute("index"),
            'list' => $this->getRoute("list"),
            'save' => $this->getRoute("save"),
            'add-sitemap' => $this->getRoute("add-sitemap"),
            'edit' => $this->getRoute("edit"),
            'preview' => $this->getRoute("preview"),
            'edit-approve' => $this->getRoute("edit-approve"),
            'approve' => $this->getRoute("approve"),
            'delete' => $this->getRoute("delete"),
            'change-language' => $this->getRoute("change-language"),
            'change-page-type' => $this->getRoute("change-page-type"),
        ];
    }
}
