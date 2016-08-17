<?php
namespace Frontend42\Selector;

use Cocur\Slugify\Slugify;
use Core42\Selector\AbstractSelector;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class SlugSelector extends AbstractSelector
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * @param Page $page
     * @return SlugSelector
     */
    public function setPage(Page $page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return string
     */
    public function getResult()
    {
        /** @var Sitemap $sitemap */
        $sitemap = $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary($this->page->getSitemapId());

        $result = $this->getTableGateway(SitemapTableGateway::class)->select(function (Select $select) use ($sitemap){
            $select->where(function (Where $where) use ($sitemap){
                if ($sitemap->getParentId() == null) {
                    $where->isNull('parentId');
                    return;
                }

                $where->equalTo("parentId", $sitemap->getParentId());
            });
        });
        $sitemapIds = [];
        foreach ($result as $sitemap) {
            $sitemapIds[] = $sitemap->getId();
        }

        $i = 0;
        do {
            $name = $this->page->getName();
            if ($i > 0) {
                $name .= " ".$i;
            }
            $slug = $this->getServiceManager()->get(Slugify::class)->slugify($name);

            $result = $this
                ->getTableGateway(PageTableGateway::class)
                ->select(function (Select $select) use ($slug, $sitemapIds){
                    $select->where(['sitemapId' => $sitemapIds]);
                    $select->where(function(Where $where) use ($slug){
                        $where->equalTo("locale", $this->page->getLocale());
                        $where->notEqualTo('id', $this->page->getId());
                        $where->equalTo("slug", $slug);
                    });
                });

            $found = ($result->count() > 0);
            $i++;
        } while ($found == true);

        return $slug;
    }

}
