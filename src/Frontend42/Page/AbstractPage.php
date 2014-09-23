<?php
namespace Frontend42\Page;

use Admin42\FormElements\Wysiwyg;
use Frontend42\Filter\UrlPath;
use Frontend42\Form\PageAddForm;
use Frontend42\Model\Tree;
use Frontend42\Model\TreeLanguage;
use Frontend42\TableGateway\TreeLanguageTableGateway;
use Frontend42\TableGateway\TreeTableGateway;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;

abstract class AbstractPage implements PageInterface
{
    /**
     * @var TreeTableGateway
     */
    protected $treeTableGateway;

    /**
     * @var TreeLanguageTableGateway
     */
    protected $treeLanguageTableGateway;

    /**
     * @var array
     */
    protected $defaultParams = array();

    /**
     * @var string
     */
    protected $routeClass = "segment";

    /**
     * @param TreeTableGateway $treeTableGateway
     * @param TreeLanguageTableGateway $treeLanguageTableGateway
     */
    public function __construct(TreeTableGateway $treeTableGateway, TreeLanguageTableGateway $treeLanguageTableGateway)
    {
        $this->treeTableGateway = $treeTableGateway;

        $this->treeLanguageTableGateway = $treeLanguageTableGateway;
    }

    public function saveInitForm(PageAddForm $form, $locale)
    {
        $values = $form->getData();

        $dateTime = new \DateTime();

        $tree = new Tree();
        $tree->setParentId((empty($values['parentId'])) ? null: $values['parentId'])
            ->setRoot((empty($values['parentId'])))
            ->setPageType($values['pageType'])
            ->setUpdated($dateTime)
            ->setCreated($dateTime);
        $this->treeTableGateway->insert($tree);

        $this->saveRoutingInformation($tree);

        $treeLanguage = new TreeLanguage();
        $treeLanguage->setLocale($locale)
            ->setTreeId($tree->getId())
            ->setSlug($this->getUniqueSlugFromTitle($values['title'], $locale))
            ->setTitle($values['title'])
            ->setStatus(TreeLanguage::STATUS_INACTIVE);

        $this->treeLanguageTableGateway->insert($treeLanguage);
    }

    protected function getUniqueSlugFromTitle($title, $locale)
    {
        $iteration = 0;

        $urlPathFilter = new UrlPath();
        $slug = $urlPathFilter->filter($title);
        $baseSlug = $slug;

        do {
            if ($iteration > 0) {
                $slug = $baseSlug.'-'.$iteration;
            }
            $result = $this->treeLanguageTableGateway->select(array(
                'locale' => $locale,
                'slug' => $slug,
            ));
            $exist = ($result->count() > 0);
            $exist = false;
            $iteration++;
        } while ($exist == true);

        return $slug;
    }

    protected function saveRoutingInformation(Tree $tree)
    {
        $tree->setRoute('{slug_'.$tree->getId().'}')
            ->setRouteClass($this->routeClass)
            ->setDefaultParams(json_encode($this->defaultParams));

        $this->treeTableGateway->update($tree);
    }

    public function getEditForm($id, $locale)
    {
        $form = new Form();

        $pageForm = new Form();
        $pageForm->setWrapElements(true)
            ->setName("page");

        $title = new Text("title");
        $title->setLabel("label.title");
        $pageForm->add($title);

        $status = new Select("status");
        $status->setLabel("label.status");
        $status->setValueOptions(array(
            TreeLanguage::STATUS_ACTIVE => 'Online',
            TreeLanguage::STATUS_INACTIVE => 'Offline',
        ));
        $pageForm->add($status);

        $metaDescription = new Textarea("metaDescription");
        $metaDescription->setLabel("label.meta-description");
        $pageForm->add($metaDescription);

        $metaKeywords = new Textarea("metaKeywords");
        $metaKeywords->setLabel("label.meta-keywords");
        $pageForm->add($metaKeywords);

        $form->add($pageForm);
        return $form;
    }

    public function saveEditForm($data, $id, $locale)
    {
        $result = $this->treeLanguageTableGateway->select(array(
            'treeId' => $id,
            'locale' => $locale
        ));
        if ($result->count() > 0) {
            $treeLanguage = $result->current();
        } else {
            $treeLanguage = new TreeLanguage();
            $treeLanguage->setTreeId($id)
                    ->setLocale($locale);
        }

        $treeLanguage->setTitle($data['page']["title"]);
        $treeLanguage->setMetaDescription($data['page']["metaDescription"]);
        $treeLanguage->setMetaKeywords($data['page']["metaKeywords"]);
        $treeLanguage->setStatus($data['page']["status"]);
        $treeLanguage->setSlug($this->getUniqueSlugFromTitle($data['page']["title"], $locale));

        if ($treeLanguage->getId() > 0) {
            $this->treeLanguageTableGateway->update($treeLanguage);
        } else {
            $this->treeLanguageTableGateway->insert($treeLanguage);
        }
    }
}
