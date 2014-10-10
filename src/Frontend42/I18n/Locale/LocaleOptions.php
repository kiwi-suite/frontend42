<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\I18n\Locale;

use Zend\Stdlib\AbstractOptions;

class LocaleOptions extends AbstractOptions
{
    const SELECTION_LANGUAGE = 'language';
    const SELECTION_LOCALE = 'locale';

    /**
     * @var string
     */
    protected $default;

    /**
     * @var string
     */
    protected $selection;

    /**
     * @var array
     */
    protected $list = array();

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return string
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param $selection
     * @throws \Exception
     */
    public function setSelection($selection)
    {
        if (!in_array($selection, array(self::SELECTION_LANGUAGE, self::SELECTION_LOCALE))) {
            throw new \Exception("invalid locale selection");
        }
        $this->selection = $selection;
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param array $list
     */
    public function setList($list)
    {
        $this->list = $list;
    }
}
