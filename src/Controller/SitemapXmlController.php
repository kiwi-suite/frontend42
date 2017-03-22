<?php
namespace Frontend42\Controller;

use Core42\Mvc\Controller\AbstractActionController;
use Zend\Http\Headers;
use Zend\Http\Response\Stream;

class SitemapXmlController extends AbstractActionController
{
    public function xmlAction()
    {
        $sitemapFileLocation = $this->getServiceManager()->get("config")['sitemap-xml']['location'];
        $sitemapFileLocation = rtrim($sitemapFileLocation, '/') . '/';
        $sitemapFileLocation .= $this->params()->fromRoute("filename") . ".xml";

        if (!file_exists($sitemapFileLocation)) {
            return $this->notFoundAction();
        }

        $stream = new Stream();
        $stream->setStream(fopen($sitemapFileLocation, 'r'));
        $stream->setStatusCode(Stream::STATUS_CODE_200);
        $stream->setStreamName(basename($sitemapFileLocation));

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Type' => 'text/xml',
            'Content-Length' => filesize($sitemapFileLocation)
        ));
        $stream->setHeaders($headers);


        return $stream;
    }
}
