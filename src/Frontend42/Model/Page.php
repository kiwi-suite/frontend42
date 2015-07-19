<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;

/**
 * @method Page setId() setId(int $id)
 * @method int getId() getId()
 * @method Page setSitemapId() setSitemapId(int $sitemapId)
 * @method int getSitemapId() getSitemapId()
 * @method Page setLocale() setLocale(string $locale)
 * @method string getLocale() getLocale()
 * @method Page setName() setName(string $name)
 * @method string getName() getName()
 * @method Page setPublishedFrom() setPublishedFrom(\DateTime $publishedFrom)
 * @method \DateTime getPublishedFrom() getPublishedFrom()
 * @method Page setPublishedUntil() setPublishedUntil(\DateTime $publishedUntil)
 * @method \DateTime getPublishedUntil() getPublishedUntil()
 * @method Page setStatus() setStatus(string $status)
 * @method string getStatus() getStatus()
 * @method Page setSlug() setSlug(string $slug)
 * @method string getSlug() getSlug()
 * @method Page setRoute() setRoute(string $route)
 * @method string getRoute() getRoute()
 * @method Page setViewCount() setViewCount(int $viewCount)
 * @method string getViewCount() getViewCount()
 * @method Page setUpdated(\DateTime $updated)
 * @method string getUpdated()
 * @method Page setUpdatedBy(int $updatedBy)
 * @method int getUpdatedBy()
 * @method Page setCreated(\DateTime $created)
 * @method string getCreated()
 * @method Page setCreatedBy(int $createdBy)
 * @method int getCreatedBy()
 */
class Page extends AbstractModel
{
    const STATUS_ONLINE = 'online';
    const STATUS_OFFLINE = 'offline';

    /**
     * @var array
     */
    protected $properties = array(
        'id',
        'sitemapId',
        'locale',
        'name',
        'publishedFrom',
        'publishedUntil',
        'status',
        'slug',
        'route',
        'viewCount',
        'updated',
        'updatedBy',
        'created',
        'createdBy',
    );


}
