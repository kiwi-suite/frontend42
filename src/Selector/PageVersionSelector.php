<?php
namespace Frontend42\Selector;

use Core42\Db\ResultSet\ResultSet;
use Core42\Selector\AbstractDatabaseSelector;
use Core42\Stdlib\DateTime;
use Frontend42\Model\PageVersion;
use Frontend42\TableGateway\PageVersionTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class PageVersionSelector extends AbstractDatabaseSelector
{
    const VERSION_HEAD = 'head';
    const VERSION_APPROVED = 'approved';

    /**
     * @var string
     */
    protected $versionId = self::VERSION_HEAD;

    /**
     * @var int
     */
    protected $pageId;

    /**
     * @param string $versionId
     * @return $this;
     */
    public function setVersionId($versionId)
    {
        if (!in_array($versionId, [self::VERSION_HEAD, self::VERSION_APPROVED])) {
            $versionId = (int) $versionId;
        }

        $this->versionId = $versionId;

        return $this;
    }

    /**
     * @param $pageId
     * @return $this
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        $result = $this->getTableGateway(PageVersionTableGateway::class)->selectWith($this->getSelect());

        if ($result->count() > 0) {
            return $result->current();
        }

        $pageVersion = new PageVersion();
        $pageVersion->setContent([])
            ->setPageId($this->pageId)
            ->setVersionName(0)
            ->setCreated(new DateTime());

        return $pageVersion;
    }

    /**
     * @return Select|string|ResultSet
     */
    protected function getSelect()
    {
        $select = $this->getTableGateway(PageVersionTableGateway::class)->getSql()->select();

        $select->where(function (Where $where) {
            $where->equalTo('pageId', $this->pageId);

            if ($this->versionId === self::VERSION_APPROVED) {
                $where->isNotNull('approved');
            } elseif (is_int($this->versionId)) {
                $where->equalTo('id', $this->versionId);
            }
        });

        if ($this->versionId === self::VERSION_HEAD) {
            $select->order('created DESC, versionName DESC');
        }

        $select->limit(1);

        return $select;
    }
}
