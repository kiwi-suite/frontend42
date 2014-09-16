<?php
namespace Frontend42\Navigation\Provider;

use Core42\Navigation\Container;
use Core42\Navigation\Page\PageFactory;
use Core42\Navigation\Provider\AbstractProvider;
use Frontend42\Model\TreeLanguage;
use Frontend42\TableGateway\TreeLanguageTableGateway;
use Frontend42\Tree\Tree;
use Zend\Db\Sql\Select;

class DatabaseProvider extends AbstractProvider
{
    /**
     * @var Tree
     */
    protected $treeReceiver;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var TreeLanguageTableGateway
     */
    protected $treeLanguageTableGateway;

    /**
     * @param Tree $treeReceiver
     * @param TreeLanguageTableGateway $treeLanguageTableGateway
     */
    public function __construct(Tree $treeReceiver, TreeLanguageTableGateway $treeLanguageTableGateway)
    {
        $this->treeReceiver = $treeReceiver;

        $this->treeLanguageTableGateway = $treeLanguageTableGateway;
    }

    /**
     * @param string $containerName
     * @return Container
     */
    public function getContainer($containerName)
    {
        if ($this->container instanceof Container) {
            return $this->container;
        }

        $this->container = new Container();
        $this->container->setContainerName($containerName);

        $result = $this->treeLanguageTableGateway->select(function(Select $select){
            $select->where
                    ->nest()
                        ->isNull('publishedFrom')
                        ->or
                        ->lessThanOrEqualTo('publishedFrom', date('Y-m-d H:i:s', time()))
                    ->unnest()
                    ->and
                    ->nest()
                        ->isNull('publishedUntil')
                        ->or
                        ->greaterThanOrEqualTo('publishedUntil', date('Y-m-d H:i:s', time()))
                    ->unnest()
                    ->and
                    ->equalTo('status', 'active')
                    ->and
                    ->equalTo('locale', \Locale::getDefault());
        });

        $treeLanguage = array();
        /** @var TreeLanguage $treeLanguageObj */
        foreach ($result as $treeLanguageObj) {
            $treeLanguage[$treeLanguageObj->getTreeId()] = $treeLanguageObj;
        }

        $pages = $this->buildNavigation($this->treeReceiver->getTree(), $treeLanguage);
        foreach ($pages as $page) {
            $this->container->addPage(PageFactory::create($page, $containerName));
        }

        $this->container->sort();
        return $this->container;
    }

    protected function buildNavigation($tree, array $treeLanguage, $routePrefix = "")
    {
        $pages = array();

        foreach ($tree as $_tree) {
            /** @var \Frontend42\Model\Tree $treeModel */
            $treeModel = $_tree['model'];

            if (!array_key_exists($treeModel->getId(), $treeLanguage)) {
                continue;
            }

            $route = $routePrefix . 'page_' .$treeModel->getId();

            $page = array(
                'options' => array(
                    'label' => $treeLanguage[$treeModel->getId()]->getTitle(),
                    'route' => $route,
                    'pageId' => $treeModel->getId(),
                    'metaDescription' => $treeLanguage[$treeModel->getId()]->getMetaDescription(),
                    'metaKeywords' => $treeLanguage[$treeModel->getId()]->getMetaKeywords(),
                ),
            );

            if (!empty($_tree['children'])) {

                $page['pages'] = $this->buildNavigation(
                    $_tree['children'],
                    $treeLanguage,
                    $route . '/'
                );
            }

            $pages[$treeModel->getId()] = $page;
        }

        return $pages;
    }
}
