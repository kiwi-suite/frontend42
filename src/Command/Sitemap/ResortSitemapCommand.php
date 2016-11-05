<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Frontend42\Model\Sitemap;
use Frontend42\TableGateway\SitemapTableGateway;

class ResortSitemapCommand extends AbstractCommand
{
    /**
     * @var array
     */
    protected $sitemapArray = [];

    /**
     * @var Sitemap[]
     */
    protected $sitemapCache = [];

    /**
     * @param array $sitemapArray
     * @return $this
     */
    public function setSitemapArray($sitemapArray)
    {
        $this->sitemapArray = $sitemapArray;

        return $this;
    }

    protected function preExecute()
    {
        $result = $this->getTableGateway(SitemapTableGateway::class)->select();

        foreach ($result as $sitemap) {
            $this->sitemapCache[$sitemap->getId()] = $sitemap;
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $left = 1;
        foreach ($this->sitemapArray as $data) {
            $left = $this->regenerateRecursive($data, $left) + 1;
        }

        $this->getCommand(UpdateNestedInfoCommand::class)->run();
    }

    protected function regenerateRecursive($data, $left, $parentId = null)
    {
        $sitemap = $this->sitemapCache[$data['id']];
        $sitemap->setNestedLeft($left);
        $sitemap->setParentId($parentId);

        if (!empty($data['items'])) {
            foreach ($data['items'] as $subData) {
                $left = $this->regenerateRecursive($subData, $left + 1, $sitemap->getId());
            }
        }
        $sitemap->setNestedRight($left + 1);

        $this->getTableGateway(SitemapTableGateway::class)->update($sitemap);

        return $sitemap->getNestedRight();
    }
}
