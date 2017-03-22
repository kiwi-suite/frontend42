<?php
namespace Frontend42\Command\SitemapXml;

use Core42\Command\AbstractCommand;
use Core42\Command\ConsoleAwareTrait;
use Thepixeldeveloper\Sitemap\Output;
use Thepixeldeveloper\Sitemap\Sitemap;
use Thepixeldeveloper\Sitemap\SitemapIndex;
use Thepixeldeveloper\Sitemap\Url;
use Thepixeldeveloper\Sitemap\Urlset;
use Zend\Stdlib\ArrayUtils;
use ZF\Console\Route;

class GenerateCommand extends AbstractCommand
{
    use ConsoleAwareTrait;

    /**
     * @var bool
     */
    protected $transaction = false;

    /**
     * @return mixed
     */
    protected function execute()
    {
        $sitemapFileLocation = $this->getServiceManager()->get("config")['sitemap-xml']['location'];
        $sitemapFileLocation = rtrim($sitemapFileLocation, '/') . '/';

        if (!file_exists($sitemapFileLocation)) {
            mkdir($sitemapFileLocation, 0777, true);
        }

        $selectorArray = $this->getServiceManager()->get("config")['sitemap-xml']['selector'];

        $locs = [];
        foreach ($selectorArray as $selector) {
            $locs = ArrayUtils::merge($locs, $this->getSelector($selector)->getResult());
        }

        $urlSets = [];
        $urlSet = new Urlset();
        $urlSets[] = $urlSet;

        $i = 0;
        /** @var Url $loc */
        foreach ($locs as $loc) {
            if (!($loc instanceof Url)) {
                continue;
            }

            if ($i > 20000) {
                $i = 0;
                $urlSet = new Urlset();
                $urlSets[] = $urlSet;
            }

            $urlSet->addUrl($loc);
            $i++;
        }

        $httpRouter = $this->getServiceManager()->get('HttpRouter');
        $baseRoute = $this->getServiceManager()->get('config')['project']['project_base_url'];
        $baseRoute = rtrim($baseRoute, '/');

        $sitemapIndex = new SitemapIndex();

        for ($i = 0; $i < count($urlSets); $i++) {
            /** @var Urlset $urlSet */
            $urlSet = $urlSets[$i];

            $filename = $sitemapFileLocation . "sitemap_" . $i . ".xml";
            file_put_contents($filename, (new Output())->getOutput($urlSet));

            $url = $baseRoute . $httpRouter->assemble(
                [
                    'filename' => "sitemap_" . $i,
                ],
                [
                    'name' => 'sitemap-xml'
                ]
            );
            $sitemapIndex->addSitemap(new Sitemap($url));
        }

        $filename = $sitemapFileLocation . "sitemap.xml";
        file_put_contents($filename, (new Output())->getOutput($sitemapIndex));
    }

    /**
     * @param Route $route
     * @return void
     */
    public function consoleSetup(Route $route)
    {

    }
}
