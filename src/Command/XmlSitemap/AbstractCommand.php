<?php
namespace  Frontend42\Command\XmlSitemap;

use Core42\Command\AbstractCommand as Core42Command;
use Zend\Http\Client;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Uri\Http as HttpUri;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use ZendTest\XmlRpc\Server\Exception;

abstract class AbstractCommand extends Core42Command
{
    protected $f;

    protected $htmlSitemapPrefix = 'html_sitemap_';

    /**
     * @var TreeRouteStack
     */
    protected $router;

    static protected $htmlSitemapItemCount = 0;

    static protected $htmlSitemapIndex = 0;

    static protected $htmlSitemapSubIndex = 0;

    static protected $htmlSubSitemaps = [];

    static protected $htmlSitemapItems = [];

    /**
     * @param string $filename
     * @param bool $isIndex
     * @param bool $isImage
     * @throws \Exception
     */
    protected function createFile($filename, $isIndex = false, $isImage = false)
    {
        $elementName = ($isIndex) ? 'sitemapindex' : 'urlset' ;

        if ($this->f !== null) {
            fwrite($this->f, "</{$elementName}>\n");
            fclose($this->f);
        }

        $this->f = fopen('public/sitemap/' . $filename, 'w');
        if ($this->f === false) {
            throw new \Exception('unable to open file');
        }

        $elementTag = "<{$elementName} xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"";
        $elementTag .= " xmlns:xhtml=\"http://www.w3.org/1999/xhtml\"";
        if ($isImage) {
            $elementTag .= " xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\"";
        }
        if ($isIndex) {
            $elementTag .= ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"';
        } else {
            $elementTag .= ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"';
        }

        $elementTag .= ">\n";

        fwrite($this->f, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
        fwrite($this->f, $elementTag);
    }

    /**
     * @param string $url
     * @param string|null $lastMod
     */
    protected function writeSitemap($url, $lastMod = null)
    {
        fwrite($this->f, "<sitemap>\n");

        fwrite($this->f, "    <loc>{$url}</loc>\n");
        if (!empty($lastMod)) {
            fwrite($this->f, "    <lastmod>{$lastMod}</lastmod>\n");
        }

        fwrite($this->f, "</sitemap>\n");
    }

    /**
     * @param string $url
     * @param string|null $lastMod
     * @param string|null $changeFreq
     * @param string|null $priority
     * @param string|null $alternate
     */
    protected function writeUrl($url, $lastMod = null, $changeFreq = null, $priority = null, $alternate = null)
    {
        fwrite($this->f, "<url>\n");

        fwrite($this->f, "    <loc>{$url}</loc>\n");
        if (!empty($lastMod)) {
            fwrite($this->f, "    <lastmod>{$lastMod}</lastmod>\n");
        }
        if (!empty($changeFreq)) {
            fwrite($this->f, "    <changefreq>{$changeFreq}</changefreq>\n");
        }
        if ($priority !== null) {
            fwrite($this->f, "    <priority>{$priority}</priority>\n");
        }
        if ($alternate !== null) {
            fwrite($this->f, "    <xhtml:link rel=\"alternate\" href=\"{$alternate}\" />\n");
        }

        fwrite($this->f, "</url>\n");
    }

    /**
     * @param string|$url
     * @param array $images
     */
    protected function writeImageUrl($url, $images)
    {
        fwrite($this->f, "<url>\n");

        fwrite($this->f, "    <loc>{$url}</loc>\n");
        foreach ($images as $image) {
            fwrite($this->f, "    <image:image>\n");
            fwrite($this->f, "        <image:loc>{$image}</image:loc>\n");
            fwrite($this->f, "    </image:image>\n");
        }

        fwrite($this->f, "</url>\n");
    }

    /**
     * @param string $url
     */
    protected function addHtmlUrl($url)
    {
        self::$htmlSitemapItems[] = $url;
        self::$htmlSitemapItemCount++;

        if (count(self::$htmlSitemapItems) == 150) {
            $this->generateHtmlSitemap(self::$htmlSitemapItems);
            self::$htmlSitemapItems = [];
        }
    }

    /**
     *
     */
    protected function finishHtmlSubSitemap()
    {
        if (count(self::$htmlSitemapItems) == 0) {
            return;
        }

        $this->generateHtmlSitemap(self::$htmlSitemapItems);
        self::$htmlSitemapItems = [];
    }

    /**
     *
     */
    protected function finishHtmlSitemap()
    {
        $this->finishHtmlSubSitemap();

        if (count(self::$htmlSubSitemaps) > 0) {
            $this->generateHtmlSitemap(self::$htmlSubSitemaps, true);
            self::$htmlSubSitemaps = [];
        }
    }

    /**
     * @param array $items
     * @param bool $isIndex
     * @throws \Exception
     */
    protected function generateHtmlSitemap($items, $isIndex = false)
    {
        if ($isIndex) {
            $filename = sprintf("%s%d.html", $this->htmlSitemapPrefix, self::$htmlSitemapIndex);
            self::$htmlSitemapIndex++;
            self::$htmlSitemapSubIndex = 0;

            $lastIndex = count($items) - 1;

            $indexFrom = $items[0]['indexFrom'];
            $indexTo = $items[$lastIndex]['indexTo'];
        } else {
            $filename = sprintf("%s%d_%d.html", $this->htmlSitemapPrefix, self::$htmlSitemapIndex, self::$htmlSitemapSubIndex);
            self::$htmlSitemapSubIndex++;

            $indexFrom = self::$htmlSitemapItemCount - count($items) + 1;
            $indexTo = self::$htmlSitemapItemCount;
        }

        $layout = new ViewModel();
        $layout->setTemplate('layout/html-sitemap.phtml');

        $template = new ViewModel();
        $template->setTemplate('susi-frontend/htmlsitemap/index.phtml');
        $template->setVariable('indexFrom', $indexFrom);
        $template->setVariable('indexTo', $indexTo);
        $template->setVariable('isIndex', $isIndex);
        $template->setVariable('items', $items);

        $layout->addChild($template);

        $viewResolver = $this->getServiceManager()->get('ViewResolver');

        $phpRenderer = new PhpRenderer();
        $phpRenderer->setResolver($viewResolver);

        $content = $phpRenderer->render($template);
        $layout->setVariable('content', $content);

        $html = $phpRenderer->render($layout);
        file_put_contents('public/sitemap/' . $filename, $html);

        if (!$isIndex) {
            self::$htmlSubSitemaps[] = [
                'url' => $this->getUrl('frontend/home') . 'sitemap/' . $filename,
                'indexFrom' => $indexFrom,
                'indexTo' => $indexTo,
            ];

            if (count(self::$htmlSubSitemaps) == 150) {
                $this->generateHtmlSitemap(self::$htmlSubSitemaps, true);
                self::$htmlSubSitemaps = [];
            }
        }
    }

    /**
     * @return TreeRouteStack
     */
    protected function getRouter()
    {
        if ($this->router === null) {
            $this->router = $this->getServiceManager()->get('HttpRouter');
        }
        return $this->router;
    }

    /**
     * @param string $route
     * @param array $params
     * @param array $options
     * @return string
     */
    protected function getUrl($route, $params = [], $options = [])
    {
        $this->getRouter();

        $uri = new HttpUri();
        if ($options['project_base_url']) {
            $uri->parse($options['project_base_url']);
            unset($options['project_base_url']);
        } else {
            $uri->setScheme('http');
        }

        $options['name'] = $route;
        $options['uri'] = $uri;

        $url = $this->router->assemble($params, $options);
        return htmlspecialchars($url, ENT_QUOTES);
    }

    /**
     * @param string $sitemapUrl
     */
    protected function ping($sitemapUrl)
    {
        //Google
        $client = new Client();
        $client->setUri('http://www.google.com/webmasters/sitemaps/ping');
        $client->setParameterGet(['sitemap' => $sitemapUrl]);
        $client->send();

        //Bing / MSN
        $client = new Client();
        $client->setUri('http://www.bing.com/webmaster/ping.aspx');
        $client->setParameterGet(['siteMap' => $sitemapUrl]);
        $client->send();

        //ASK
        $client = new Client();
        $client->setUri('http://submissions.ask.com/ping');
        $client->setParameterGet(['sitemap' => $sitemapUrl]);
        $client->send();
    }
}
