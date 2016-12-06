<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Frontend42\Model\Sitemap;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Where;

class DeleteSitemapCommand extends AbstractCommand
{
    /**
     * @var int
     */
    protected $sitemapId;

    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * @param int $sitemapId
     * @return $this
     */
    public function setSitemapId($sitemapId)
    {
        $this->sitemapId = $sitemapId;

        return $this;
    }

    protected function preExecute()
    {
        $this->sitemap = $this->getTableGateway(SitemapTableGateway::class)->selectByPrimary((int) $this->sitemapId);

        if (empty($this->sitemap)) {
            $this->addError("sitemap", "empty sitemap");
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $delete = $this->getTableGateway(SitemapTableGateway::class)->getSql()->delete();
        $delete->where(function(Where $where) {
            $where->between('nestedLeft', $this->sitemap->getNestedLeft(), $this->sitemap->getNestedRight());
        });
        $this->getTableGateway(SitemapTableGateway::class)->deleteWith($delete);

        $nestedCalc = $this->sitemap->getNestedRight() - $this->sitemap->getNestedLeft() + 1;

        $update = $this->getTableGateway(SitemapTableGateway::class)->getSql()->update();
        $update->set([
            'nestedLeft' => new Expression("nestedLeft - " . $nestedCalc),
        ]);
        $update->where(function (Where $where) {
            $where->greaterThan("nestedLeft", $this->sitemap->getNestedRight());
        });
        $this->getTableGateway(SitemapTableGateway::class)->updateWith($update);

        $update = $this->getTableGateway(SitemapTableGateway::class)->getSql()->update();
        $update->set([
            'nestedRight' => new Expression("nestedRight - " . $nestedCalc),
        ]);
        $update->where(function (Where $where) {
            $where->greaterThan("nestedRight", $this->sitemap->getNestedRight());
        });
        $this->getTableGateway(SitemapTableGateway::class)->updateWith($update);

        $this->getCommand(UpdateNestedInfoCommand::class)->run();
    }
}
