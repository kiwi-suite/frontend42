<?php
namespace Frontend42\Controller;

use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\View\Model\JsonModel;
use Frontend42\Command\Sitemap\EditPageCommand;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeProvider;
use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;

class SitemapController extends AbstractAdminController
{
    public function indexAction()
    {
        $this->getCommand('Frontend42\Sitemap\AddMissingPages')->run();
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
                'locale'    => $_item['page']->getLocale(),
                'title'     => $_item['page']->getLocale() . ' ' . $_item['sitemap']->getId(),
                'status'    => $_item['page']->getStatus(),
                'viewCount' => $_item['page']->getViewCount(),
                'pageType'  => $_item['sitemap']->getPageType(),
                'droppable' => !$_item['sitemap']->getTerminal(),
                'items'     => [],
            ];
            if (!empty($_item['children'])) {
                $node['items'] = $this->prepareJsonTree($_item['children']);
            }

            $tree[] = $node;
        }

        return $tree;
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
        $page = $this->getTableGateway('Frontend42\Page')
            ->selectByPrimary((int) $this->params()->fromPost('page_selector'));


        $this->getCommand('Frontend42\Sitemap\AddSitemap')
            ->setPageType($this->params()->fromPost('page_type_selector'))
            ->setParentId($page->getSitemapId())
            ->run();

        return new JsonModel(['success' => true]);
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

                /** @var EditPageCommand $cmd */
                $cmd = $this->getCommand('Frontend42\Sitemap\EditPage');
                $cmd->setPage($page)
                    ->setCreatedUser($authenticationService->getIdentity())
                    ->setContent($pageForm->getInputFilter()->getValues());

                $pageVersion = $cmd->run();
            }
        } else {
            $pageVersion = $this->getSelector('Frontend42\PageVersion')->setPageId($page->getId())->getResult();
            $pageForm->setData(Json::decode($pageVersion->getContent(), Json::TYPE_ARRAY));
        }

        return [
            'sections' => $pageTypeProvider->getDisplayFormSections($sitemap->getPageType()),
            'pageForm' => $pageForm,
        ];
    }
}
