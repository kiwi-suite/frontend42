<?php
namespace Frontend42\Command\Navigation;

use Core42\Command\AbstractCommand;
use Frontend42\Model\Navigation;
use Frontend42\TableGateway\NavigationTableGateway;

class SaveNavigationCommand extends AbstractCommand
{
    /**
     * @var int
     */
    protected $pageId;

    /**
     * @var array
     */
    protected $navs = [];

    /**
     * @param int $pageId
     * @return SaveNavigationCommand
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * @param array $navs
     * @return SaveNavigationCommand
     */
    public function setNavs(array $navs)
    {
        $this->navs = $navs;

        return $this;
    }

    protected function preExecute()
    {
        $this->pageId = (int) $this->pageId;
        if (empty($this->pageId)) {
            $this->addError("pageId", "empty pageId");
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $this->getTableGateway(NavigationTableGateway::class)->delete(['pageId' => $this->pageId]);

        foreach ($this->navs as $nav) {
            $navigation = new Navigation();
            $navigation->setPageId($this->pageId)
                ->setNav($nav);

            $this->getTableGateway(NavigationTableGateway::class)->insert($navigation);
        }
    }
}
