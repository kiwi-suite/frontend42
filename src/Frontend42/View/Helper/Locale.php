<?php
namespace Frontend42\View\Helper;

use Frontend42\I18n\Locale\LocaleOptions;
use Zend\View\Helper\AbstractHelper;

class Locale extends AbstractHelper
{
    /**
     * @var LocaleOptions
     */
    private $localeOptions;

    /**
     * @param LocaleOptions $localeOptions
     */
    public function __construct(LocaleOptions $localeOptions)
    {
        $this->localeOptions = $localeOptions;
    }

    public function getLocaleArray()
    {
        $return = array();
        $list = $this->localeOptions->getList();

        foreach ($list as $locale) {
            $return[$locale] = ($this->localeOptions->getSelection() == LocaleOptions::SELECTION_LANGUAGE)
                ? \Locale::getDisplayLanguage($locale)
                : \Locale::getDisplayRegion($locale);
        }

        return $return;
    }
}
