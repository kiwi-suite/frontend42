<?php
namespace Frontend42\Command\PageVersion;

use Admin42\Model\User;
use Core42\Command\AbstractCommand;
use Frontend42\Command\Router\CreateRouteConfigCommand;
use Frontend42\Event\SitemapEvent;
use Frontend42\Model\Page;
use Frontend42\Model\PageVersion;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeContent;
use Frontend42\PageType\PageTypeProvider;
use Frontend42\Selector\PageVersionSelector;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\PageVersionTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Json\Json;

class ApproveCommand extends AbstractCommand
{
    /**
     * @var int
     */
    protected $versionId;

    /**
     * @var PageVersion
     */
    protected $version;

    /**
     * @param int $versionId
     * @return ApproveCommand
     */
    public function setVersionId($versionId)
    {
        $this->versionId = $versionId;
        return $this;
    }

    /**
     * @param PageVersion $version
     * @return ApproveCommand
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function preExecute()
    {
        if ((int) $this->versionId > 0) {
            $this->version = $this
                ->getTableGateway(PageVersionTableGateway::class)
                ->selectByPrimary((int) $this->versionId);
        }

        if (empty($this->version)) {
            $this->addError("version", "invalid version");

            return;
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $this
            ->getTableGateway(PageVersionTableGateway::class)
            ->update(['approved' => null], ['pageId' => $this->version->getPageId()]);

        $this->version->setApproved(new \DateTime());

        $this->getTableGateway(PageVersionTableGateway::class)->update($this->version);

        return $this->version;
    }
}
