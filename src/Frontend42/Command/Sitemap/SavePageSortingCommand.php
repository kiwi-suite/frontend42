<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Frontend42\Event\SitemapEvent;
use Frontend42\Model\Sitemap;
use Zend\Json\Json;
use Zend\Stdlib\ArrayUtils;

class SavePageSortingCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $jsonTreeString;

    /**
     * @var array
     */
    protected $flatTree = [];

    /**
     * @param string $jsonTreeString
     * @return $this
     */
    public function setJsonTreeString($jsonTreeString)
    {
        $this->jsonTreeString = $jsonTreeString;

        return $this;
    }

    /**
     *
     */
    protected function preExecute()
    {
        if ($this->jsonTreeString !== null) {
            $this->flatTree = $this->flattenTree(Json::decode($this->jsonTreeString, Json::TYPE_ARRAY));
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        if (empty($this->flatTree)) {
            return;
        }

        $result = $this->getSelector('Frontend42\Sitemap')
            ->setLocale($this->getServiceManager()->get('Localization')->getDefaultLocale())
            ->getResult();

        $this->recursiveSave($result);

        $this
            ->getServiceManager()
            ->get('Frontend42\Sitemap\EventManager')
            ->trigger(SitemapEvent::EVENT_SORTING_CHANGE);

        $this->getCommand('Frontend42\Router\CreateRouteConfig')->run();
    }

    /**
     * @param array $items
     */
    protected function recursiveSave(array $items)
    {
        foreach ($items as $item) {
            /** @var Sitemap $sitemap */
            $sitemap = $item['sitemap'];

            if (!array_key_exists($sitemap->getId(), $this->flatTree)) {
                continue;
            }

            $sitemapNewOptions = $this->flatTree[$sitemap->getId()];
            $sitemap->setParentId($sitemapNewOptions['parentId'])
                ->setOrderNr($sitemapNewOptions['orderNr'])
                ->setUpdated(new \DateTime());

            $this->getTableGateway('Frontend42\Sitemap')->update($sitemap);

            if (!empty($item['children'])) {
                $this->recursiveSave($item['children']);
            }
        }
    }

    /**
     * @param array $items
     * @param null|int $parent
     * @return array
     */
    protected function flattenTree(array $items, $parent = null)
    {
        $tree = [];
        for ($i = 1; $i <= count($items); $i++) {
            $item = $items[$i - 1];
            $tree[$item['id']] = [
                'parentId' => $parent,
                'orderNr' => $i
            ];
            if (!empty($item['items'])) {
                $tree = ArrayUtils::merge($tree, $this->flattenTree($item['items'], (int) $item['id']));
            }
        }

        return $tree;
    }
}
