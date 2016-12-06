<?php
namespace Frontend42\Controller;

use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\I18n\Localization\Localization;
use Frontend42\Command\Page\EditPageCommand;
use Frontend42\Command\PageVersion\ApproveCommand;
use Frontend42\Command\PageVersion\DeleteCommand;
use Frontend42\Form\EditPageForm;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\PageVersionTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Select;
use Zend\Http\PhpEnvironment\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Console\Router\RouteMatch;
use Zend\Mvc\MvcEvent;

class PageController extends AbstractAdminController
{
    /**
     * @return array
     */
    public function editAction()
    {
        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        /** @var Page $page */
        $page = $this
            ->getTableGateway(PageTableGateway::class)
            ->selectByPrimary((int) $this->params()->fromRoute('id'));

        if (empty($page)) {
            return $this->notFoundAction();
        }

        /** @var Sitemap $sitemap */
        $sitemap = $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary($page->getSitemapId());
        if (empty($sitemap)) {
            return $this->notFoundAction();
        }

        $version = $this->getSelector(PageVersionSelector::class)
            ->setVersionId($this->params()->fromRoute('versionId', PageVersionSelector::VERSION_HEAD))
            ->setPageId($page->getId())
            ->getResult();

        /** @var PageTypeInterface $pageType */
        $pageType = $this->getServiceManager()->get(PageTypePluginManager::class)->get($sitemap->getPageType());

        /** @var EditPageForm $form */
        $form = $this->getForm(EditPageForm::class);
        $form->setSections($pageType->getSections())
            ->setDefaults($pageType->getDefaults())
            ->addPageElements();

        if ($prg !== false) {
            $approve = (isset($prg['__save__']) && $prg['__save__'] == 'approve');
            $form->setData($prg);
            if ($form->isValid()) {
                $content = $form->getDataForDatabase();
                $cmd = $this->getCommand(EditPageCommand::class)
                    ->setPage($page)
                    ->setSitemap($sitemap)
                    ->setUser($this->getIdentity())
                    ->setContent($content)
                    ->setCurrentVersion($version)
                    ->setApprove($approve);

                $cmd->run();
                if (!$cmd->hasErrors()) {
                    $this->flashMessenger()->addSuccessMessage([
                        'title' => 'frontend42.toaster.page.edit.title.success',
                        'message' => 'frontend42.toaster.page.edit.message.success',
                    ]);

                    return $this
                        ->redirect()
                        ->toRoute('admin/page/edit', ['id' => $page->getId()]);
                }
            }

            $this->flashMessenger()->addErrorMessage([
                'title' => 'frontend42.toaster.page.edit.title.error',
                'message' => 'frontend42.toaster.page.edit.message.error',
            ]);

        } else {
            $form->setDatabaseData($version->getContent());
        }

        $versions = $this
            ->getTableGateway(PageVersionTableGateway::class)
            ->select(function(Select $select) use ($page) {
                $select->where(['pageId' => $page->getId()]);
                $select->order('created DESC');
        });
        $versions->buffer();

        return [
            'page'              => $page,
            'currentVersion'    => $version,
            'pageForm'          => $form,
            'versions'          => $versions,
        ];
    }

    public function previewAction()
    {
        /** @var Page $page */
        $page = $this
            ->getTableGateway(PageTableGateway::class)
            ->selectByPrimary((int) $this->params()->fromRoute('id'));

        if (empty($page)) {
            return $this->notFoundAction();
        }

        /** @var Sitemap $sitemap */
        $sitemap = $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary($page->getSitemapId());
        if (empty($sitemap)) {
            return $this->notFoundAction();
        }

        /** @var PageTypeInterface $pageType */
        $pageType = $this->getServiceManager()->get(PageTypePluginManager::class)->get($sitemap->getPageType());

        if ($this->getRequest()->isPost()) {
            /** @var EditPageForm $form */
            $form = $this->getForm(EditPageForm::class);
            $form->setSections($pageType->getSections())
                ->setDefaults($pageType->getDefaults())
                ->addPageElements();

            $data = $this->getRequest()->getPost()->toArray();
            $form->setData($data);
            if (!$form->isValid()) {

                return [
                    'pageForm' => $form,
                ];
            }

            $pageContent = $pageType->getPageContent($form->getDataForDatabase(), $page);
        } else {
            $version = $this->getSelector(PageVersionSelector::class)
                ->setVersionId($this->params()->fromRoute('versionId', PageVersionSelector::VERSION_HEAD))
                ->setPageId($page->getId())
                ->getResult();

            $pageContent = $pageType->getPageContent($version->getContent(), $page);
        }

        $pageContent = $pageType->mutate($pageContent);
        $this->layout($pageType->getLayout());

        /** @var MvcEvent $mvcEvent */
        $mvcEvent = $this->getServiceManager()->get('Application')->getMvcEvent();

        $localization = $this->getServiceManager()->get(Localization::class);
        $localization->acceptLocale($page->getLocale());
        $this->getServiceManager()->get(TranslatorInterface::class)->setLocale($localization->getActiveLocale());

        /** @var RouteMatch $routeMatch */
        $routeMatch = $mvcEvent->getRouteMatch();
        $routeMatch->setParam("__page__", $page);
        $routeMatch->setParam("__sitemap__", $sitemap);
        $routeMatch->setParam("__pageContent__", $pageContent);

        $result = $this->forward()->dispatch($pageType->getController(), [
            'action' => $pageType->getAction(),
            '__page__' => $page,
            '__sitemap__' => $sitemap,
            '__pageContent__' => $pageContent,
        ]);

        return $result;
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function changeLocaleAction()
    {
        $result = $this->getTableGateway(PageTableGateway::class)->select([
            'locale' => $this->params("locale"),
            'sitemapId' => $this->params('sitemapId')
        ]);

        if ($result->count() > 0) {
            return $this->redirect()->toRoute('admin/page/edit', ['id' => $result->current()->getId()]);
        }

        return $this->notFoundAction();
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function approveAction()
    {
        $version = $this
            ->getCommand(ApproveCommand::class)
            ->setVersionId($this->params()->fromRoute('versionId'))->run();

        if (empty($version)) {
            return $this->notFoundAction();
        }

        $this->flashMessenger()->addSuccessMessage([
            'title' => 'frontend42.toaster.page-version.approve.title.success',
            'message' => 'frontend42.toaster.page-version.approve.message.success',
        ]);

        return $this
            ->redirect()
            ->toRoute('admin/page/edit', ['id' => $version->getPageId(), 'versionId' => $version->getId()]);
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function deleteVersionAction()
    {
        /** @var DeleteCommand $cmd */
        $cmd = $this->getCommand(DeleteCommand::class);
        $cmd->setVersionId($this->params()->fromRoute('versionId'))
            ->run();

        if ($cmd->hasErrors()) {
            $this->flashMessenger()->addErrorMessage([
                'title' => 'frontend42.toaster.page-version.delete.title.error',
                'message' => 'frontend42.toaster.page-version.delete.message.error',
            ]);
        } else {
            $this->flashMessenger()->addSuccessMessage([
                'title' => 'frontend42.toaster.page-version.delete.title.success',
                'message' => 'frontend42.toaster.page-version.delete.message.success',
            ]);
        }

        return $this
            ->redirect()
            ->toRoute('admin/page/edit', ['id' => $this->params()->fromRoute('id')]);
    }
}
