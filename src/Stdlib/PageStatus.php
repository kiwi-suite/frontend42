<?php
namespace Frontend42\Stdlib;

use Core42\Stdlib\DateTime;

abstract class PageStatus
{
    /**
     * @param $publishedFrom
     * @param $publishedUntil
     * @return bool
     */
    public static function isPublished($publishedFrom, $publishedUntil)
    {
        if (empty($publishedFrom) && empty($publishedUntil)) {
            return true;
        }

        if (empty($publishedFrom) && $publishedUntil instanceof DateTime && $publishedUntil->getTimestamp() > time()) {
            return true;
        }

        if (empty($publishedUntil) && $publishedFrom instanceof DateTime && $publishedFrom->getTimestamp() < time()) {
            return true;
        }

        if ($publishedFrom instanceof DateTime
            && $publishedUntil instanceof DateTime
            && $publishedFrom->getTimestamp() < time()
            && $publishedUntil->getTimestamp() > time()
        ) {
            return true;
        }

        return false;
    }
}
