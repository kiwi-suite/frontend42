<?php
namespace Frontend42\Command\Sitemap;

use Core42\Command\AbstractCommand;
use Core42\I18n\Localization\Localization;
use Frontend42\Model\Page;
use Zend\Db\Sql\Where;

class AddMissingPagesCommand extends AbstractCommand
{
    /**
     * @var array
     */
    protected $missing = [];

    /**
     *
     */
    protected function preExecute()
    {
        /** @var Localization $localization */
        $localization = $this->getServiceManager()->get("Localization");

        $availableLocales = $localization->getAvailableLocales();

        foreach ($availableLocales as $locale) {
            $pageTableGateway = $this->getTableGateway('Frontend42\Page');
            $sql = $this->getTableGateway('Frontend42\Sitemap')->getSql();

            $select = $sql->select();
            $select->columns(array('id'));
            $select->where(function (Where $where) use ($locale, $pageTableGateway) {
                $select = $pageTableGateway->getSql()->select();
                $select->columns(['sitemapId']);
                $select->where(['locale' => $locale]);
                $where->notIn('id', $select);
            });
            $statement = $sql->prepareStatementForSqlObject($select);

            $result = $statement->execute();

            foreach ($result as $res) {
                if (!array_key_exists($locale, $this->missing)) {
                    $this->missing[$locale] = [];
                }

                $this->missing[$locale][] = $res['id'];
            }
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        if (count($this->missing) == 0) {
            return;
        }

        foreach ($this->missing as $locale => $sitemapArr) {
            foreach ($sitemapArr as $sitemapId) {
                $page = new Page();
                $page->setLocale($locale)
                    ->setSitemapId($sitemapId)
                    ->setRoute("[]")
                    ->setStatus("offline");

                $this->getTableGateway('Frontend42\Page')->insert($page);
            }
        }

        $this->getCommand('Frontend42\Router\CreateRouteConfig')->run();
    }
}
