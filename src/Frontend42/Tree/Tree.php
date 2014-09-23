<?php
namespace Frontend42\Tree;

use Frontend42\Model\TreeLanguage;
use Frontend42\TableGateway\TreeLanguageTableGateway;
use Frontend42\TableGateway\TreeTableGateway;

class Tree
{
    /**
     * @var TreeTableGateway
     */
    protected $treeTableGateway;

    /**
     * @var TreeLanguageTableGateway
     */
    protected $treeLanguageTableGateway;

    /**
     * @var array|null
     */
    protected $tree = null;

    /**
     * @var array
     */
    protected $treeLocale = array();

    /**
     * @param TreeTableGateway $treeTableGateway
     * @param TreeLanguageTableGateway $treeLanguageTableGateway
     */
    public function __construct(TreeTableGateway $treeTableGateway, TreeLanguageTableGateway $treeLanguageTableGateway)
    {
        $this->treeTableGateway = $treeTableGateway;

        $this->treeLanguageTableGateway = $treeLanguageTableGateway;
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

    /**
     * @param $locale
     * @return array
     */
    public function getTreeWithLocale($locale)
    {
        if (isset($this->treeLocale[$locale])) {
            return $this->treeLocale[$locale];
        }

        $result = $this->treeLanguageTableGateway->select(array(
            'locale' => $locale
        ));

        /** @var TreeLanguage[] $treeLanguage */
        $treeLanguage = array();
        /** @var TreeLanguage $treeLanguageObj */
        foreach ($result as $treeLanguageObj) {
            $treeLanguage[$treeLanguageObj->getTreeId()] = $treeLanguageObj;
        }

        $tree = $this->getTree();

        $recursiveFunction = function(&$tree) use (&$recursiveFunction, $treeLanguage){
            foreach ($tree as &$_tree) {
                if (isset($treeLanguage[$_tree['model']->getId()])) {
                    $_tree['language'] = $treeLanguage[$_tree['model']->getId()];
                }

                if (!empty($_tree['children'])) {
                    $recursiveFunction($_tree['children']);
                }
            }
        };
        $recursiveFunction($tree);
        $this->treeLocale[$locale] = $tree;

        return $this->treeLocale[$locale];
    }
}
