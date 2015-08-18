<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Frontend42\Model\Sitemap;

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

    protected function preExecute()
    {
        if ($this->sitemapId > 0) {
            $this->sitemap = $this->getTableGateway('Frontend42\Sitemap')->selectByPrimary((int) $this->sitemapId);
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
        $this->getTableGateway('Frontend42\Sitemap')->delete($this->sitemap);


        $this->getCommand('Frontend42\Router\CreateRouteConfig')->run();
    }
}
