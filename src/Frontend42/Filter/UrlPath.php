<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;
use Zend\Filter\PregReplace;

class UrlPath extends AbstractFilter
{

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        $value = mb_strtolower(trim($value), 'UTF-8');
        $value = str_replace(" ", "-", $value);
        $value = str_replace("ö", "oe", $value);
        $value = str_replace("ä", "ae", $value);
        $value = str_replace("ü", "ue", $value);
        $value = str_replace("ß", "ss", $value);

        $pattern = '/[^a-zA-Z0-9\+_-]/';

        $pregReplace = new PregReplace();
        $pregReplace->setPattern($pattern);
        $pregReplace->setReplacement("");

        return $pregReplace->filter($value);
    }
}
