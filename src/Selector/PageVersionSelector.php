<?php
namespace Frontend42\Selector;

use Core42\Db\ResultSet\ResultSet;
use Core42\Selector\AbstractDatabaseSelector;
use Frontend42\Model\PageVersion;
use Frontend42\TableGateway\PageVersionTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Json\Json;

class PageVersionSelector extends AbstractDatabaseSelector
{
    const VERSION_HEAD = 'head';
    const VERSION_APPROVED = 'approved';

    /**
     * @var string
     */
    protected $versionName = self::VERSION_HEAD;

    /**
     * @var int
     */
    protected $pageId;

    /**
     * @param string $versionName
     * @return $this;
     */
    public function setVersionName($versionName)
    {
        if (!in_array($versionName, [self::VERSION_HEAD, self::VERSION_APPROVED])) {
            $versionName = (int) $versionName;
        }

        $this->versionName = $versionName;

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
        $pageVersion->setContent(Json::encode([]))
            ->setPageId($this->pageId)
            ->setVersionId(1);

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

            if ($this->versionName === self::VERSION_APPROVED) {
                $where->isNotNull('approved');
            } elseif (is_int($this->versionName)) {
                $where->equalTo('versionId', $this->versionName);
            }
        });

        if ($this->versionName === self::VERSION_HEAD) {
            $select->order('versionId DESC');
        }

        $select->limit(1);

        return $select;
    }
}
