<?php
namespace Frontend42\Controller;

use Core42\Mvc\Controller\AbstractActionController;

class FrontendController extends AbstractActionController
{
    public function indexAction()
    {
        $result = $this->getTableGateway('Frontend42\Tree')->select();
        $flatTree = array();
        foreach ($result as $treeEntry) {
            $flatTree[$treeEntry->getId()] = array(
                'model' => $treeEntry,
                'children' => array()
            );
        }

        $tree = array();
        foreach ($flatTree as &$treeEntry) {
            if ($treeEntry['model']->getParentId() > 0) {
                $parent =& $flatTree[$treeEntry['model']->getParentId()];
                $parent['children'][] = $treeEntry;

                continue;
            }

            $tree[] =& $treeEntry;
        }

        var_dump($tree);

        return $this->getResponse();
    }
}
