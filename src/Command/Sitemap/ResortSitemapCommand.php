<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Frontend42\PageType\Service\PageTypePluginManager;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;

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
        $result = $this->getTableGateway(SitemapTableGateway::class)->select(function (Select $select) {
            $select->order("nestedLeft ASC");
        });

        /** @var Sitemap $sitemap */
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

        $prevNestedLeft = $sitemap->getNestedLeft();

        $sitemap->setNestedLeft($left);
        $sitemap->setParentId($parentId);

        /** @var PageTypeInterface $pageType */
        $pageType = $this->getServiceManager()->get(PageTypePluginManager::class)->get($sitemap->getPageType());

        if ($pageType->isTerminal() === true) {
            if ($prevNestedLeft != $sitemap->getNestedLeft()) {
                $diff = $sitemap->getNestedLeft() - $prevNestedLeft;

                $sql = sprintf(
                    "UPDATE frontend42_sitemap SET nestedLeft = nestedLeft + %d, nestedRight = nestedRight + %d WHERE nestedLeft > %d AND nestedRight < %d",
                    $diff,
                    $diff,
                    $prevNestedLeft,
                    $sitemap->getNestedRight()
                );
                $adapter = $this->getTableGateway(SitemapTableGateway::class)->getAdapter();
                $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

                $diff = $sitemap->getNestedRight() - $prevNestedLeft;
                $left = $sitemap->getNestedLeft() + $diff - 1;
            }
        } else if (!empty($data['items'])) {
            foreach ($data['items'] as $subData) {
                $left = $this->regenerateRecursive($subData, $left + 1, $sitemap->getId());
            }
        }
        $sitemap->setNestedRight($left + 1);

        $this->getTableGateway(SitemapTableGateway::class)->update($sitemap);

        return $sitemap->getNestedRight();
    }
}
