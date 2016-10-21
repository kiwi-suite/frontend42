<?php
namespace Frontend42\Controller;

use Admin42\Mvc\Controller\AbstractAdminController;
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
        $form->addPageElements($pageType->getSections());

        if ($prg !== false) {
            $approve = (isset($prg['__save__']) && $prg['__save__'] == 'approve');
            $form->setData($prg);
            if ($form->isValid()) {
                $content = $form->getData();
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

                $this->flashMessenger()->addErrorMessage([
                    'title' => 'frontend42.toaster.page.edit.title.error',
                    'message' => 'frontend42.toaster.page.edit.message.error',
                ]);
            }
        } else {
            $form->setDatabaseData($pageType->getSections(), $version->getContent());
        }

        $versions = $this
            ->getTableGateway(PageVersionTableGateway::class)
            ->select(function(Select $select) use ($page) {
                $select->where(['pageId' => $page->getId()]);
                $select->order('created DESC');
        });

        return [
            'page'              => $page,
            'currentVersion'    => $version,
            'pageForm'          => $form,
            'versions'          => $versions,
        ];
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
