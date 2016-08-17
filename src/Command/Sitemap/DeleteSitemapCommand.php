<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Frontend42\Event\SitemapEvent;
use Frontend42\Model\Sitemap;
use Frontend42\TableGateway\SitemapTableGateway;

class DeleteSitemapCommand extends AbstractCommand
{
    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * @var int
     */
    protected $sitemapId;

    /**
     * @param Sitemap $sitemap
     * @return $this
     */
    public function setSitemap($sitemap)
    {
        $this->sitemap = $sitemap;

        return $this;
    }

    /**
     * @param int $sitemapId
     * @return $this
     */
    public function setSitemapId($sitemapId)
    {
        $this->sitemapId = $sitemapId;

        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function preExecute()
    {
        if ($this->sitemapId > 0) {
            $this->sitemap = $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary((int) $this->sitemapId);
        }

        if (empty($this->sitemap)) {
            $this->addError("sitemap", "invalid sitemap");

            return;
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $this->getTableGateway(SitemapTableGateway::class)->delete($this->sitemap);
    }
}
