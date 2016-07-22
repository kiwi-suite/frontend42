<?php
namespace  Frontend42\Command\XmlSitemap;

use Core42\Command\ConsoleAwareTrait;
use ZF\Console\Route;

class GenerateCommand extends AbstractCommand
{
    use ConsoleAwareTrait;

    protected function execute()
    {
        $config = $this->getServiceManager()->get('Config');

        $sitemaps = [];
        foreach ($config['xml_sitemap']['commands'] as $name) {
            
            $cmd = $this->getCommand($name);
            $sitemaps = array_merge($sitemaps, $cmd->run());
        }

        if (count($sitemaps) === 1) {
            //rename();
        } else {
            $this->createFile('sitemap.xml', true);

            $baseUrl = $config['project']['project_base_url'];

            foreach ($sitemaps as $sitemap) {
                $url =  $baseUrl . '/sitemap/' . $sitemap['filename'];
                $this->writeSitemap($url, $sitemap['updated']);
            }

            fwrite($this->f, "</sitemapindex>\n");
            fclose($this->f);
        }
    }

    public function consoleSetup(Route $route)
    {
    }
}
