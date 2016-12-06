<?php
namespace Frontend42\Command\PageVersion;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Core42\Stdlib\DateTime;
use Frontend42\Model\PageContent;
use Frontend42\Model\PageVersion;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\PageVersionTableGateway;

class CreateCommand extends AbstractCommand
{
    /**
     * @var int
     */
    protected $pageId;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var PageContent
     */
    protected $pageContent;

    /**
     * @var PageVersion
     */
    protected $previousVersion;

    /**
     * @param int $pageId
     * @return $this
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param PageContent $pageContent
     * @return $this
     */
    public function setPageContent($pageContent)
    {
        $this->pageContent = $pageContent;

        return $this;
    }

    /**
     * @param PageVersion $previousVersion
     * @return $this
     */
    public function setPreviousVersion(PageVersion $previousVersion)
    {
        $this->previousVersion = $previousVersion;

        return $this;
    }

    /**
     * @return PageVersion
     */
    protected function execute()
    {
        if ($this->previousVersion instanceof PageVersion) {
            $this->previousVersion->setContent($this->pageContent->toArray());

            if (!$this->previousVersion->hasChanged('content')) {
                return $this->previousVersion;
            }
        }

        $headVersion = $this->getSelector(PageVersionSelector::class)
            ->setVersionId(PageVersionSelector::VERSION_HEAD)
            ->setPageId($this->pageId)
            ->getResult();

        $pageVersion = new PageVersion();
        $pageVersion->setVersionName($headVersion->getVersionName() + 1)
            ->setContent($this->pageContent->toArray())
            ->setPageId($this->pageId)
            ->setCreated(new DateTime())
            ->setCreatedBy($this->user->getId());

        $this->getTableGateway(PageVersionTableGateway::class)->insert($pageVersion);

        return $pageVersion;

    }
}
