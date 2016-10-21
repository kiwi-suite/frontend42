<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;
use Core42\Stdlib\DateTime;

/**
 * @method Page setId() setId(int $id)
 * @method int getId() getId()
 * @method Page setSitemapId() setSitemapId(int $sitemapId)
 * @method int getSitemapId() getSitemapId()
 * @method Page setLocale() setLocale(string $locale)
 * @method string getLocale() getLocale()
 * @method Page setName() setName(string $name)
 * @method string getName() getName()
 * @method Page setPublishedFrom() setPublishedFrom(DateTime $publishedFrom)
 * @method DateTime getPublishedFrom() getPublishedFrom()
 * @method Page setPublishedUntil() setPublishedUntil(DateTime $publishedUntil)
 * @method DateTime getPublishedUntil() getPublishedUntil()
 * @method Page setStatus() setStatus(string $status)
 * @method string getStatus() getStatus()
 * @method Page setSlug() setSlug(string $slug)
 * @method string getSlug() getSlug()
 * @method Page setUpdated() setUpdated(DateTime $updated)
 * @method DateTime getUpdated() getUpdated()
 * @method Page setCreated() setCreated(DateTime $created)
 * @method DateTime getCreated() getCreated()
 */
class Page extends AbstractModel
{
    const STATUS_ONLINE = 'online';
    const STATUS_OFFLINE = 'offline';

    /**
     * @var array
     */
    public $properties = [
        'id',
        'sitemapId',
        'locale',
        'name',
        'publishedFrom',
        'publishedUntil',
        'status',
        'slug',
        'updated',
        'created',
    ];


}
