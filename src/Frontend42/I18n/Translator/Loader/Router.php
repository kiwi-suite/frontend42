<?php
namespace Frontend42\I18n\Translator\Loader;

use Frontend42\Model\TreeLanguage;
use Frontend42\TableGateway\TreeLanguageTableGateway;
use Zend\I18n\Translator\Loader\RemoteLoaderInterface;
use Zend\I18n\Translator\TextDomain;

class Router implements RemoteLoaderInterface
{
    /**
     * @var TreeLanguageTableGateway
     */
    private $treeLanguageTableGateway;

    /**
     * @param TreeLanguageTableGateway $treeLanguageTableGateway
     */
    public function __construct(TreeLanguageTableGateway $treeLanguageTableGateway)
    {
        $this->treeLanguageTableGateway = $treeLanguageTableGateway;
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
        $result = $this->treeLanguageTableGateway->select(array('locale' => $locale));
        $messages = array();

        /** @var TreeLanguage $treeLanguage */
        foreach ($result as $treeLanguage) {
            $messages['slug_' . $treeLanguage->getTreeId()] = (string) $treeLanguage->getSlug();
        }
        return new TextDomain($messages);
    }
}
