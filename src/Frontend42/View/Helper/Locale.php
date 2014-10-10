<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

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

    /**
     * @return array
     */
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
