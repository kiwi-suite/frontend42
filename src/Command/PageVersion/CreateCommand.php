<?php
namespace Frontend42\Command\PageVersion;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Frontend42\Model\PageVersion;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\PageVersionTableGateway;
use Zend\Json\Json;

class CreateCommand extends AbstractCommand
{
    /**
     * @var int
     */
    protected $pageId;

    /**
     * @var User
     */
    protected $createdBy;

    /**
     * @var array
     */
    protected $content;

    /**
     * @var PageVersion
     */
    protected $previousVersion;

    /**
     * @param int $pageId
     * @return CreateCommand
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
        return $this;
    }

    /**
     * @param User $createdBy
     * @return CreateCommand
     */
    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @param array $content
     * @return CreateCommand
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param PageVersion $previousVersion
     * @return CreateCommand
     */
    public function setPreviousVersion($previousVersion)
    {
        $this->previousVersion = $previousVersion;
        return $this;
    }

    /**
     *
     */
    protected function preExecute()
    {
        if (empty($this->previousVersion)) {
            $this->previousVersion = $this
                ->getSelector(PageVersionSelector::class)
                ->setPageId($this->pageId)
                ->setVersionName(PageVersionSelector::VERSION_HEAD)
                ->getResult();
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $pageVersion = new PageVersion();
        $pageVersion->setVersionId($this->previousVersion->getVersionId() + 1)
            ->setContent($this->content)
            ->setPageId($this->pageId)
            ->setCreated(new \DateTime())
            ->setCreatedBy($this->createdBy->getId());

        $this->getTableGateway(PageVersionTableGateway::class)->insert($pageVersion);

        return $pageVersion;
    }
}
