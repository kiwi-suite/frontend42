<?php
namespace Frontend42\Tree;

use Frontend42\TableGateway\TreeTableGateway;

class Tree
{
    /**
     * @var TreeTableGateway
     */
    protected $treeTableGateway;

    /**
     * @var array|null
     */
    protected $tree = null;

    /**
     * @param TreeTableGateway $treeTableGateway
     */
    public function __construct(TreeTableGateway $treeTableGateway)
    {
        $this->treeTableGateway = $treeTableGateway;
    }

    /**
     * @return array
     */
    public function getTree()
    {
        if ($this->tree === null) {
            $this->loadTree();
        }

        return $this->tree;
    }

    /**
     *
     */
    protected function loadTree()
    {
        $result = $this->treeTableGateway->select();
        $flatTree = array();

        /** @var \Frontend42\Model\Tree $treeEntry */
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
                $parent['children'][] =& $treeEntry;

                continue;
            }

            $tree[] =& $treeEntry;
        }

        $this->tree = $tree;
    }
}
