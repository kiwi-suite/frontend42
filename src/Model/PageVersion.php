<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;

/**
 * @method PageVersion setId() setId(int $id)
 * @method int getId() getId()
 * @method PageVersion setVersionName() setVersionName(int $versionName)
 * @method int getVersionName() getVersionName()
 * @method PageVersion setPageId() setPageId(int $pageId)
 * @method int getPageId() getPageId()
 * @method PageVersion setContent() setContent(string $content)
 * @method string getContent() getContent()
 * @method PageVersion setApproved() setApproved(\DateTime $approved)
 * @method \DateTime getApproved() getApproved()
 * @method PageVersion setCreatedBy() setCreatedBy(int $createdBy)
 * @method int getCreatedBy() getCreatedBy()
 * @method PageVersion setCreated() setCreated(\DateTime $created)
 * @method \DateTime getCreated() getCreated()
 */
class PageVersion extends AbstractModel
{

    /**
     * @var array
     */
    public $properties = [
        'id',
        'versionName',
        'pageId',
        'content',
        'approved',
        'createdBy',
        'created',
    ];


}
