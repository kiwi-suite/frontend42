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
 * @method Page setExcludeMenu() setExcludeMenu(boolean $excludeMenu)
 * @method boolean getExcludeMenu() getExcludeMenu()
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
 * @method int getViewCount() getViewCount()
 * @method Page setUpdated() setUpdated(\DateTime $updated)
 * @method \DateTime getUpdated() getUpdated()
 * @method Page setUpdatedBy() setUpdatedBy(int $updatedBy)
 * @method int getUpdatedBy() getUpdatedBy()
 * @method Page setCreated() setCreated(\DateTime $created)
 * @method \DateTime getCreated() getCreated()
 * @method Page setCreatedBy() setCreatedBy(int $createdBy)
 * @method int getCreatedBy() getCreatedBy()
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
        'excludeMenu',
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