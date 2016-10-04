<?php
namespace Frontend42\PageType;

use Core42\I18n\Localization\Localization;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageContent\PageContent;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

class LocalizationPageType extends AbstractPageType
{
    /**
     * @var Localization
     */
    protected $localization;

    public function __construct(Localization $localization)
    {
        $this->localization = $localization;
    }

    /**
     * @param Page $page
     * @param PageContent $pageContent
     * @param Sitemap $sitemap
     * @return array
     */
    public function getRouting(Page $page, PageContent $pageContent, Sitemap $sitemap)
    {
        $locale = $page->getLocale();

        $localizationPart = \Locale::getPrimaryLanguage($locale);
        $localizationPartName = 'language';
        $localizationCondition = array_map(function ($value){
            return \Locale::getPrimaryLanguage($value);
        }, $this->localization->getAvailableLocales());

        if ($this->localization->getType() == Localization::TYPE_REGION) {
            $localizationPart = \Locale::getPrimaryLanguage($locale);
            $localizationPartName = 'locale';
            $localizationCondition = $this->localization->getAvailableLocales();
        }

        $routing = [
            'type' => Literal::class,
            'options' => [
                'route' => '/'.$localizationPart.'/',
                'defaults' => [
                    'controller' => $this->getController(),
                    'action' => $this->getAction(),
                    $localizationPartName => $localizationPart,
                ]
            ]
        ];

        if ($locale == $this->localization->getDefaultLocale()) {
            $routing['type'] = Segment::class;
            $routing['options']['route'] = '/[:'.$localizationPartName.'/]';
            $routing['options']['constraints'] = [
                $localizationPartName => '('.implode("|", $localizationCondition).')'
            ];
        }

        return $routing;
    }
}
