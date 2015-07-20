<?php
namespace Frontend42\Controller;

use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\View\Model\JsonModel;
use Frontend42\Command\Sitemap\AddSitemapCommand;
use Frontend42\Command\Sitemap\ApproveCommand;
use Frontend42\Command\Sitemap\EditPageCommand;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeContent;
use Frontend42\PageType\PageTypeProvider;
use Frontend42\Selector\PageVersionSelector;
use Zend\Db\Sql\Select;
use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\View\Model\ViewModel;

class SitemapController extends AbstractAdminController
{
    public function indexAction()
    {
        return [
            'createForm' => $this->getForm('Frontend42\Sitemap\Create'),
        ];
    }

    public function listAction()
    {
        $jsonString = $this->getRequest()->getContent();
        $options = Json::decode($jsonString, Json::TYPE_ARRAY);

        $result = $this->getSelector('Frontend42\Sitemap')
            ->setLocale($options['locale'])
            ->getResult();

        return new JsonModel($this->prepareJsonTree($result));
    }

    protected function prepareJsonTree($items)
    {
        $tree = [];
        foreach ($items as $_item) {
            $node = [
                'id'        => $_item['sitemap']->getId(),
                'pageId'    => $_item['page']->getId(),
                'locale'    => $_item['page']->getLocale(),
                'title'     => $_item['page']->getName(),
                'status'    => $_item['page']->getStatus(),
                'viewCount' => $_item['page']->getViewCount(),
                'pageType'  => $_item['sitemap']->getPageType(),
                'droppable' => !$_item['sitemap']->getTerminal(),
                'items'     => [],
            ];
            if (!empty($_item['children']) && !$_item['sitemap']->getTerminal()) {
                $node['items'] = $this->prepareJsonTree($_item['children']);
            }

            $tree[] = $node;
        }

        return $tree;
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isDelete()) {
            $deleteCmd = $this->getCommand('Frontend42\Sitemap\DeleteSitemap');

            $deleteParams = array();
            parse_str($this->getRequest()->getContent(), $deleteParams);

            $deleteCmd->setSitemapId((int) $deleteParams['id'])
                ->run();

            return new JsonModel(array(
                'success' => true,
            ));
        } elseif ($this->getRequest()->isPost()) {
            $deleteCmd = $this->getCommand('Frontend42\Sitemap\DeleteSitemap');

            $deleteCmd->setSitemapId((int) $this->params()->fromPost('id'))
                ->run();

            $this->flashMessenger()->addSuccessMessage([
                'title' => 'toaster.sitemap.delete.title.success',
                'message' => 'toaster.sitemap.delete.message.success',
            ]);

            return new JsonModel([
                'redirect' => $this->url()->fromRoute('admin/sitemap')
            ]);
        }

        return new JsonModel([
            'redirect' => $this->url()->fromRoute('admin/sitemap')
        ]);
    }

    public function saveAction()
    {
        $this->getCommand('Frontend42\Sitemap\SavePageSorting')
            ->setJsonTreeString($this->getRequest()->getContent())
            ->run();

        return new JsonModel(['success' => true]);
    }

    public function addSitemapAction()
    {
        $authenticationService = $this->getServiceLocator()->get('Admin42\Authentication');

        /* @var AddSitemapCommand $cmd */
        $cmd = $this->getCommand('Frontend42\Sitemap\AddSitemap')
            ->setPageType($this->params()->fromPost('page_type_selector'))
            ->setCreatedUser($authenticationService->getIdentity())
            ->setName($this->params()->fromPost('name'))
            ->setParentPageId($this->params()->fromPost('page_selector'));

        $page = $cmd->run();

        return new JsonModel([
            'success' => true,
            'url' => $this->url()->fromRoute('admin/sitemap/edit', ['id' => $page->getId()])
        ]);
    }

    /**
     * @return array|\Zend\Http\Response
     * @throws \Exception
     */
    public function editAction()
    {
        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        /** @var PageTypeProvider $pageTypeProvider */
        $pageTypeProvider = $this->getServiceLocator()->get('Frontend42\PageTypeProvider');

        /** @var Page page */
        $page = $this->getTableGateway('Frontend42\Page')->selectByPrimary((int) $this->params('id'));

        /** @var Sitemap $sitemap */
        $sitemap = $this->getTableGateway('Frontend42\Sitemap')->selectByPrimary($page->getSitemapId());

        $pageForm = $pageTypeProvider->getPageForm($sitemap->getPageType());

        if ($prg !== false) {
            $pageForm->setData($prg);

            if ($pageForm->isValid()) {
                $authenticationService = $this->getServiceLocator()->get('Admin42\Authentication');

                $approve = false;
                if (array_key_exists('save', $prg)) {
                    if ($prg['save'] == 'approve') {
                        $approve = true;
                    }
                    unset($prg['save']);
                }

                /** @var EditPageCommand $cmd */
                $cmd = $this->getCommand('Frontend42\Sitemap\EditPage');
                $cmd->setPage($page)
                    ->setApprove($approve)
                    ->setUpdatedUser($authenticationService->getIdentity())
                    ->setContent($prg);

                $pageVersion = $cmd->run();
                $this->flashMessenger()->addSuccessMessage([
                    'title' => 'Page saved',
                    'message' => 'Page successfully saved',
                ]);

                return $this->redirect()->toRoute('admin/sitemap/edit', array('id' => $pageVersion->getPageId()));
            } else {
                /** @var PageVersionSelector $selector */
                $selector = $this->getSelector('Frontend42\PageVersion')->setPageId($page->getId());
                if ($this->params()->fromRoute('version') !== null) {
                    $selector->setVersionName($this->params()->fromRoute("version"));
                }
                $pageVersion = $selector->getResult();
            }
        } else {
            /** @var PageVersionSelector $selector */
            $selector = $this->getSelector('Frontend42\PageVersion')->setPageId($page->getId());
            if ($this->params()->fromRoute('version') !== null) {
                $selector->setVersionName($this->params()->fromRoute("version"));
            }
            $pageVersion = $selector->getResult();

            $pageTypeContent = new PageTypeContent();
            $formData = $pageTypeContent->generateFormData(
                $pageTypeProvider->getPageTypeOptions($sitemap->getPageType())->getForm(),
                Json::decode($pageVersion->getContent(), Json::TYPE_ARRAY)
            );

            $pageForm->setData($formData);
        }

        $versions = $this->getTableGateway('Frontend42\PageVersion')->select(function(Select $select) use($page) {
            $select->where(['pageId' => $page->getId()]);
            $select->order('created DESC');
        });
        $versions->buffer();

        $viewModel = new ViewModel([
            'sections' => $pageTypeProvider->getDisplayFormSections($sitemap->getPageType()),
            'pageForm' => $pageForm,
            'versions' => $versions,
            'currentVersion' => $pageVersion,
            'page'     => $page,
            'changePageTypeForm' => $this->getForm('Frontend42\Sitemap\ChangePageType')
        ]);
        $viewModel->setTemplate("frontend42/sitemap/edit");

        return $viewModel;
    }

    public function approveAction()
    {
        $pageId = $this->params('id');
        $pageVersionId = $this->params('version');

        $authenticationService = $this->getServiceLocator()->get('Admin42\Authentication');

        /* @var ApproveCommand $cmd*/
        $cmd = $this->getCommand('Frontend42\Sitemap\Approve');
        $cmd->setPageId($pageId)
            ->setPageVersionId($pageVersionId)
            ->setUpdatedUser($authenticationService->getIdentity())
            ->run();

        return $this->redirect()->toRoute('admin/sitemap/edit', array('id' => $pageId));
    }

    public function changeLanguageAction()
    {
        $result = $this->getTableGateway('Frontend42\Page')->select([
            'locale' => $this->params("locale"),
            'sitemapId' => $this->params('sitemapId')
        ]);

        if ($result->count() > 0) {
            return $this->redirect()->toRoute('admin/sitemap/edit', ['id' => $result->current()->getId()]);
        }

        return $this->redirect()->toRoute('admin/sitemap');
    }

    public function changePageTypeAction()
    {
        $authenticationService = $this->getServiceLocator()->get('Admin42\Authentication');
        
        $this->getCommand('Frontend42\Sitemap\ChangePageType')
            ->setPageType($this->params()->fromPost("page_type"))
            ->setSitemapId($this->params("sitemapId"))
            ->setCreatedBy($authenticationService->getIdentity()->getId())
            ->run();

        return new JsonModel([
            'redirect' => $this->url()->fromRoute('admin/sitemap/edit', ['id' => $this->params('pageId')])
        ]);
    }
}
