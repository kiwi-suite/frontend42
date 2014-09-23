<?php
namespace Frontend42\Controller;

use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\View\Model\JsonModel;
use Frontend42\Model\TreeLanguage;
use Frontend42\Page\PageInterface;
use Zend\Form\Form;
use Zend\Http\PhpEnvironment\Response;
use Zend\View\Model\ViewModel;

class TreeController extends AbstractAdminController
{
    public function indexAction()
    {

    }

    public function indexSidebarAction()
    {

    }

    public function addElementAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);

        $viewModel->form = $this->getForm('Frontend42\Content');

        return $viewModel;
    }

    public function editAction()
    {
        $viewModel = new ViewModel();
        $viewModel->locale = $this->params()->fromRoute('locale');

        $treeTableGateway = $this->getTableGateway('Frontend42\Tree');
        $tree = $treeTableGateway->selectByPrimary((int) $this->params()->fromRoute('id'));

        /** @var PageInterface $pageType */
        $pageType = $this->getServiceLocator()->get($tree->getPageType());

        $form = $pageType->getEditForm($tree->getId(), $this->params()->fromRoute('locale'));
        $viewModel->form = $form;

        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        if ($prg !== false) {
            $form->setData($prg);
            $pageType->saveEditForm($prg, $tree->getId(), $this->params()->fromRoute('locale'));
            return $this->redirect()->toRoute('admin/tree/edit', array(), true);
        } else {
            $treeLanguageTableGateway = $this->getTableGateway('Frontend42\TreeLanguage');
            $result = $treeLanguageTableGateway->select(array(
                'treeId' => $tree->getId(),
                'locale' => $this->params()->fromRoute('locale')
            ));
            if ($result->count() > 0) {
                /** @var TreeLanguage $treeLanguage */
                $treeLanguage = $result->current();
                $form->setData(array(
                    'page' => array(
                        'title' => $treeLanguage->getTitle(),
                        'status' => $treeLanguage->getStatus(),
                        'metaDescription' => $treeLanguage->getMetaDescription(),
                        'metaKeywords' => $treeLanguage->getMetaKeywords(),
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

    public function addAction()
    {
        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        $form = $this->getForm('Frontend42\PageAdd');
        $form->populateParentIdSelect($this->params()->fromRoute("locale"));

        if ($prg !== false) {
            $form->setData($prg);
            if ($form->isValid()) {
                $pageType = $this->getServiceLocator()->get($prg['pageType']);
                $pageType->saveInitForm($form, $this->params()->fromRoute("locale"));

                return $this->redirect()->toRoute('admin/tree');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->form = $form;

        return $viewModel;
    }

    public function jsonAction()
    {
        $locale = $this->params()->fromRoute('locale');

        $tree = $this->getServiceLocator()->get('Frontend42\Tree')->getTreeWithLocale($locale);

        $function = function(array $tree, $level = 0) use(&$function, $locale){
            $return = array();

            foreach ($tree as $_tree) {

                $url = $this->url()->fromRoute(
                    'admin/tree/edit',
                    array(
                        'locale' => $locale,
                        'id' => $_tree['model']->getId()
                    )
                );

                $title = "<code>#{$_tree['model']->getId()}</code> [missing title]";
                if (isset($_tree['language'])) {
                    $title = "<code>#{$_tree['model']->getId()}</code> " .$_tree['language']->getTitle();
                }

                $tmp = array(
                    'id' => $_tree['model']->getId(),
                    'text' => $title,
                    'icon' => 'fa fa-fw fa-edit',
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
