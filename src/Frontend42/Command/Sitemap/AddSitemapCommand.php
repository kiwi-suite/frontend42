<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Frontend42\Model\Sitemap;
use Frontend42\PageType\PageTypeInterface;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class AddSitemapCommand extends AbstractCommand
{
    /**
     * @var int|null
     */
    protected $parentId;

    /**
     * @var string
     */
    protected $pageType;

    /**
     * @param string $pageType
     * @return $this
     */
    public function setPageType($pageType)
    {
        $this->pageType = $pageType;

        return $this;
    }

    /**
     * @param $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @var int
     */
    protected $orderNr;

    protected function preExecute()
    {
        if ($this->parentId !== null) {
            $this->parentId = (int) $this->parentId;
        }


        $select = $this->getTableGateway('Frontend42\Sitemap')
            ->getSql()
            ->select();

        if (empty($this->parentId)) {
            $select->where(function(Where $where){
                $where->isNull('parentId');
            });
        } else {
            $select->where(['parentId' => $this->parentId]);
        }

        $select->columns(['orderNr' => new Expression('MAX(orderNr)')]);
        $statement = $this->getTableGateway('Frontend42\Sitemap')->getSql()->prepareStatementForSqlObject($select);
        $result = $statement->execute()->current();

        $this->orderNr = $result['orderNr'];
        if (empty($this->orderNr)) {
            $this->orderNr = 1;
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $sitemap = new Sitemap();
        $sitemap->setParentId($this->parentId)
            ->setPageType($this->pageType)
            ->setOrderNr($this->orderNr);

        /** @var PageTypeInterface $pageTypeObject */
        $pageTypeObject = $this->getServiceManager()->get('Frontend42\PageTypeProvider')->getPageType($this->pageType);

        $pageTypeObject->prepareForAdd($sitemap);

        var_dump($sitemap);
    }
}
