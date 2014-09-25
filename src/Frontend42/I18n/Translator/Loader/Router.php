<?php
namespace Frontend42\I18n\Translator\Loader;

use Frontend42\Model\Page;
use Frontend42\TableGateway\PageTableGateway;
use Zend\I18n\Translator\Loader\RemoteLoaderInterface;
use Zend\I18n\Translator\TextDomain;

class Router implements RemoteLoaderInterface
{
    /**
     * @var PageTableGateway
     */
    private $pageTableGateway;

    /**
     * @param PageTableGateway $pageTableGateway
     */
    public function __construct(PageTableGateway $pageTableGateway)
    {
        $this->pageTableGateway = $pageTableGateway;
    }

    /**
     * Load translations from a remote source.
     *
     * @param  string $locale
     * @param  string $textDomain
     * @return \Zend\I18n\Translator\TextDomain|null
     */
    public function load($locale, $textDomain)
    {
        $result = $this->pageTableGateway->select(array('locale' => $locale));
        $messages = array();

        /** @var Page $page */
        foreach ($result as $page) {
            if (strlen($page->getSlug()) == 0) {
                continue;
            }
            $messages['slug_' . $page->getSitemapId()] = (string) $page->getSlug();
        }
        return new TextDomain($messages);
    }
}
