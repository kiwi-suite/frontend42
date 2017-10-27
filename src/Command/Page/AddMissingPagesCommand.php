<?php
namespace Frontend42\Command\Page;

use Admin42\Model\User;
use Admin42\TableGateway\UserTableGateway;
use Core42\Command\AbstractCommand;
use Core42\Command\ConsoleAwareTrait;
use Core42\Db\ResultSet\ResultSet;
use Core42\I18n\Localization\Localization;
use Frontend42\Command\Sitemap\UpdateNestedInfoCommand;
use Frontend42\Model\Sitemap;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Db\Sql\Where;
use ZF\Console\Route;

class AddMissingPagesCommand extends AbstractCommand
{
    use ConsoleAwareTrait;

    private $missing = [];

    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $userId;

    /**
     * @param Route $route
     * @return void
     */
    public function consoleSetup(Route $route)
    {
        $this->setUserId($route->getMatchedParam('userId'));
    }

    public function setUserId($userId)
    {
        $this->userId = (int) $userId;
    }


    protected function preExecute()
    {
        if (empty($this->userId)) {
            $this->addError("userId", "invalid userId");
            return;
        }

        $this->user = $this->getTableGateway(UserTableGateway::class)->selectByPrimary($this->userId);
        if (empty($this->user)) {
            $this->addError("userId", "invalid userId");
            return;
        }

        /** @var Localization $localization */
        $localization = $this->getServiceManager()->get(Localization::class);

        foreach ($localization->getAvailableLocales() as $locale) {
            $pageTableGateway = $this->getTableGateway(PageTableGateway::class);

            $sql = $this->getTableGateway(SitemapTableGateway::class)->getSql();
            $select = $sql->select();
            $select->where(function (Where $where) use ($locale, $pageTableGateway) {
                $select = $pageTableGateway->getSql()->select();
                $select->columns(['sitemapId']);
                $select->where(['locale' => $locale]);
                $where->notIn('id', $select);
            });
            $resultSet = new ResultSet(
                $this->getTableGateway(SitemapTableGateway::class)->getHydrator(),
                $this->getTableGateway(SitemapTableGateway::class)->getModel()
            );
            $statement = $sql->prepareStatementForSqlObject($select);
            $resultSet->initialize($statement->execute());
            foreach ($resultSet as $res) {
                if (!array_key_exists($locale, $this->missing)) {
                    $this->missing[$locale] = [];
                }
                $this->missing[$locale][] = $res;
            }
        }
    }

    protected function execute()
    {
        foreach ($this->missing as $locale => $sitemapArray) {
            /** @var Sitemap $sitemap */
            foreach ($sitemapArray as $sitemap) {
                /** @var AddPageCommand $cmd */
                $cmd = $this->getCommand(AddPageCommand::class);
                $cmd->setUser($this->user);
                $cmd->setLocale($locale);
                $cmd->setSitemap($sitemap);
                $cmd->run();

                $this->consoleOutput(sprintf("Page for '%d' and locale '%s' created", $sitemap->getId(), $locale));
            }
        }

        $this->getCommand(UpdateNestedInfoCommand::class)->run();
    }
}
