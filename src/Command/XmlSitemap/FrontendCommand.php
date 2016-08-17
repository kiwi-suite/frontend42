<?php
namespace Frontend42\Command\XmlSitemap;

use Frontend42\Command\Router\CreateRouteConfigCommand;
use Frontend42\Model\Page;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use ZF\Console\Route;

class FrontendCommand extends AbstractCommand
{

    /**
     * @throws \Exception
     */
    protected function execute()
    {
        $config = $this->getServiceManager()->get('Config');

        $cache = $this->getServiceManager()->get('Cache\Sitemap');
        $now = new \DateTime();

        $cmd = $this->getCommand(CreateRouteConfigCommand::class);
        $cmd->run();

        $tableGateway = $this->getTableGateway(PageTableGateway::class);
        $select = $tableGateway->getSql()->select();
        $select->join(['s' => $this->getTableGateway(SitemapTableGateway::class)->getTable()], 's.id = frontend42_page.sitemapId', []);
        $select->where
            ->equalTo('frontend42_page.status', Page::STATUS_ONLINE);

        if (!empty($config['xml_sitemap']['page_types'])) {
            if ($config['xml_sitemap']['page_type_mode'] == 'whitelist') {
                $select->where->in('s.pageType', $config['xml_sitemap']['page_types']);
            } else {
                $select->where->notIn('s.pageType', $config['xml_sitemap']['page_types']);
            }
        }

        //echo $select->getSqlString();die();

        $pageResult = $tableGateway->selectWith($select);

        $pageMapping = [];
        if ($cache->hasItem('pageMapping')) {
            $pageMapping = $cache->getItem("pageMapping");
        }

        $sitemaps = [];
        $fileCount = 0;
        $count = 0;

        foreach ($pageResult as $page) {
            /* @var Page $page */

            $count++;

            if (!array_key_exists($page->getId(), $pageMapping)) {
                continue;
            }

            $route = $pageMapping[$page->getId()]['route'];

            if ($this->f === null || $count % 50000 == 0) {

                $filename = 'sitemap_frontend_' . $fileCount++ . ".xml";

                $this->createFile($filename);
                $sitemaps[] = [
                    'filename' => $filename,
                    'updated' => $now->format(\DateTime::W3C),
                ];
            }

            $url = $this->getUrl($route, [], ['project_base_url' => $config['project']['project_base_url']]);

            $lastMod = $page->getUpdated()->format(\DateTime::W3C);
            $this->writeUrl($url, $lastMod, null, null);

            if ($count == 500) {
                //break 2;
            }
        }

        if ($this->f !== null) {
            fwrite($this->f, "</urlset>\n");
            fclose($this->f);
        }

        return $sitemaps;
    }

    /**
     * @param Route $route
     */
    public function consoleSetup(Route $route)
    {
    }
}
