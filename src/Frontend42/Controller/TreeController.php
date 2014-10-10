<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Controller;

use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\View\Model\JsonModel;
use Frontend42\Model\Page;
use Frontend42\PageType\PageTypeInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\View\Model\ViewModel;

class TreeController extends AbstractAdminController
{
    /**
     * @return array|void
     */
    public function indexAction()
    {

    }

    /**
     *
     */
    public function indexSidebarAction()
    {

    }

    /**
     * @return ViewModel
     */
    public function addElementAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);

        $viewModel->form = $this->getForm('Frontend42\Content');

        return $viewModel;
    }

    /**
     * @return \Zend\Http\Response
     * @throws \Exception
     */
    public function previewAction()
    {
        $sitemapTableGateway = $this->getTableGateway('Frontend42\Sitemap');
        $sitemap = $sitemapTableGateway->selectByPrimary((int) $this->params()->fromRoute('id'));

        /** @var PageTypeInterface $pageType */
        $pageType = $this->getServiceLocator()->get('PageType')->get($sitemap->getPageType());

        $form = $pageType->getEditForm($sitemap->getId(), $this->params()->fromRoute('locale'));

        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        if ($prg !== false) {
            $form->setData($prg);
            $pageType->saveEditForm($prg, $sitemap->getId(), $this->params()->fromRoute('locale'), false);

            $container = $this->getServiceLocator()->get('Core42\Navigation')->getContainer('frontend42');
            //$navigation = $this->getServiceLocator()->get('Core42\Navigation');

            $page = $container->findOneByOption('sitemapId', $sitemap->getId());

            $url = $this->url()->fromRoute(
                $page->getOption('route'),
                array(
                    'locale' => $this->params()->fromRoute('locale')
                )
            );

            return $this->redirect()->toUrl($url . '?preview=true');
        }

        return $this->redirect()->toRoute('admin/tree/edit', array(), true);
    }

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function editAction()
    {
        $viewModel = new ViewModel();
        $viewModel->locale = $this->params()->fromRoute('locale');

        $sitemapTableGateway = $this->getTableGateway('Frontend42\Sitemap');
        $sitemap = $sitemapTableGateway->selectByPrimary((int) $this->params()->fromRoute('id'));

        /** @var PageTypeInterface $pageType */
        $pageType = $this->getServiceLocator()->get('PageType')->get($sitemap->getPageType());

        $form = $pageType->getEditForm($sitemap->getId(), $this->params()->fromRoute('locale'));
        $viewModel->form = $form;

        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        if ($prg !== false) {
            $form->setData($prg);
            $pageType->saveEditForm($prg, $sitemap->getId(), $this->params()->fromRoute('locale'), true);
            return $this->redirect()->toRoute('admin/tree/edit', array(), true);
        } else {
            $pageTableGateway = $this->getTableGateway('Frontend42\Page');
            $result = $pageTableGateway->select(array(
                'sitemapId' => $sitemap->getId(),
                'locale' => $this->params()->fromRoute('locale')
            ));
            if ($result->count() > 0) {
                /** @var Page $page */
                $page = $result->current();
                $form->setData(array(
                    'page' => array(
                        'title' => $page->getTitle(),
                        'status' => $page->getStatus(),
                        'metaDescription' => $page->getMetaDescription(),
                        'metaKeywords' => $page->getMetaKeywords(),
                    ),
                ));
            }
        }

        $sidebar = $this->addSidebar(
            'Frontend42\Tree',
            array(
                'action' => 'index-sidebar'
            )
        );
        $sidebar->locale = $this->params()->fromRoute('locale');

        return $viewModel;
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function addAction()
    {
        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        $form = $this->getForm('Frontend42\PageAdd');
        $form->populateLocale($this->params()->fromRoute("locale"));

        if ($prg !== false) {
            $form->setData($prg);
            if ($form->isValid()) {
                $pageType = $this->getServiceLocator()->get('PageType')->get($prg['pageType']);
                $pageType->saveInitForm($form, $this->params()->fromRoute("locale"));

                return $this->redirect()->toRoute('admin/tree');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->form = $form;

        return $viewModel;
    }

    /**
     * @return JsonModel
     */
    public function jsonAction()
    {
        $locale = $this->params()->fromRoute('locale');

        $tree = $this->getServiceLocator()->get('Frontend42\SitemapProvider')->getTreeWithLocale($locale);

        $function = function (array $tree, $level = 0) use (&$function, $locale) {
            $return = array();

            foreach ($tree as $_tree) {

                $url = $this->url()->fromRoute(
                    'admin/tree/edit',
                    array(
                        'locale' => $locale,
                        'id' => $_tree['model']->getId()
                    )
                );

                $iconOffline = 'text-danger';

                $title = "<code>#{$_tree['model']->getId()}</code> [missing title]";
                if (isset($_tree['language'])) {
                    $title = "<code>#{$_tree['model']->getId()}</code> " .$_tree['language']->getTitle();

                    if ($_tree['language']->getStatus() === Page::STATUS_ACTIVE) {
                        $iconOffline = "text-success";
                    }
                }

                $tmp = array(
                    'id' => $_tree['model']->getId(),
                    'text' => $title,
                    'icon' => 'fa fa-fw fa-circle ' . $iconOffline,
                    'state' => array(
                        'opened' => ($level < 3),
                        'disabled' => false,
                        'selected' => false,
                    ),
                    'type' => 'dropable',
                    'types' => array(
                        'dropable' => array(
                            'valid_children' => array('dropable'),
                        ),
                    ),
                    'a_attr' => array(
                        'href' => $url,
                    ),
                    'children' => array(),
                );
                if (!empty($_tree['children'])) {
                    $tmp['children'] = $function($_tree['children'], $level + 1);
                }

                $return[] = $tmp;
            }

            return $return;
        };
        $jsonModel = new JsonModel($function($tree));

        return $jsonModel;
    }
}
