<?php
namespace Frontend42\Command\PageVersion;

use Core42\Command\AbstractCommand;
use Frontend42\Model\PageVersion;
use Frontend42\TableGateway\PageVersionTableGateway;

class DeleteCommand extends AbstractCommand
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
     * @return $this
     */
    public function setVersionId($versionId)
    {
        $this->versionId = $versionId;

        return $this;
    }

    /**
     * @param PageVersion $version
     * @return $this
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

        if ($this->version->getApproved() !== null) {
            $this->addError("version", "invalid version");

            return;
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $this->getTableGateway(PageVersionTableGateway::class)->delete($this->version);
    }
}
