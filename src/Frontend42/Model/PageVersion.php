<?php
namespace Frontend42\Model;

use Core42\Model\AbstractModel;

/**
 * @method PageVersion setId() setId(int $id)
 * @method int getId() getId()
 * @method PageVersion setVersionId() setVersionId(int $versionId)
 * @method int getVersionId() getVersionId()
 * @method PageVersion setPageId() setPageId(int $pageId)
 * @method int getPageId() getPageId()
 * @method PageVersion setContent() setContent(string $content)
 * @method string getContent() getContent()
 * @method PageVersion setCreated() setCreated(\DateTime $created)
 * @method \DateTime getCreated() getCreated()
 * @method PageVersion setCreatedBy() setCreatedBy(int $createdBy)
 * @method int getCreatedBy() getCreatedBy()
 * @method PageVersion setApproved() setApproved(\DateTime $approved)
 * @method \DateTime getApproved() getApproved()
 */
class PageVersion extends AbstractModel
{

    /**
     * @var array
     */
    protected $properties = array(
        'id',
        'versionId',
        'pageId',
        'content',
        'created',
        'createdBy',
        'approved',
    );


}
