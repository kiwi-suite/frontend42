<?php
namespace Frontend42\Form;

use Frontend42\Tree\Tree;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;

class PageAddForm extends Form
{
    /**
     * @var Tree
     */
    private $treeReceiver;

    /**
     * @var array
     */
    private $pageTypeConfig;


    public function __construct(Tree $treeReceiver, $pageTypeConfig)
    {
        parent::__construct();

        $this->treeReceiver = $treeReceiver;

        $this->pageTypeConfig = $pageTypeConfig;
    }

    public function init()
    {
        $this->setName("tree");

        if (count($this->pageTypeConfig) == 1) {
            $pageType = new Hidden("pageType");
            $currentPageType = current($this->pageTypeConfig);
            $pageType->setValue($currentPageType['class']);
        } else {
            $pageType = new Select("pageType");
            $pageType->setLabel("label.pageType");
            $pageTypeValues = array();
            foreach($this->pageTypeConfig as $_pageType) {
                $pageTypeValues[$_pageType['class']] = $_pageType['name'];
            }
            $pageType->setValueOptions($pageTypeValues);
        }
        $this->add($pageType);

        $parentId = new Select("parentId");
        $parentId->setLabel("label.parentId");
        $this->add($parentId);


        $title = new Text("title");
        $title->setLabel("label.title");
        $this->add($title);
    }

    public function populateParentIdSelect($locale)
    {
        $tree = $this->treeReceiver->getTreeWithLocale($locale);

        $values = array(
            0 => "---"
        );
        $recursiveFunction = function($tree, $level = 0) use (&$recursiveFunction, &$values){
            foreach ($tree as $_tree) {

                $title = "[missing title]";
                if (isset($_tree['language'])) {
                    $title = $_tree['language']->getTitle();
                }
                $values[$_tree['model']->getId()] = str_repeat(" ", $level * 4) . $title;

                if (!empty($_tree['children'])) {
                    $tmp['children'] = $recursiveFunction($_tree['children'], $level + 1);
                }
            }
        };
        $recursiveFunction($tree);

        $this->get("parentId")->setValueOptions($values);
    }
}
