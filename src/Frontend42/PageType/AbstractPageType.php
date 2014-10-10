<?php
/**
 * frontend42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Frontend42\PageType;

use Frontend42\Filter\UrlPath;
use Frontend42\Form\PageAddForm;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\TableGateway\PageTableGateway;
use Frontend42\TableGateway\SitemapTableGateway;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;

abstract class AbstractPageType implements PageTypeInterface
{
    /**
     * @var SitemapTableGateway
     */
    protected $sitemapTableGateway;

    /**
     * @var PageTableGateway
     */
    protected $pageTableGateway;

    /**
     * @var array
     */
    protected $defaultParams = array();

    /**
     * @var string
     */
    protected $routeClass = "segment";

    /**
     * @param SitemapTableGateway $sitemapTableGateway
     */
    public function setSitemapTableGateway(SitemapTableGateway $sitemapTableGateway)
    {
        $this->sitemapTableGateway = $sitemapTableGateway;
    }

    /**
     * @param PageTableGateway $pageTableGateway
     */
    public function setPageTableGateway(PageTableGateway $pageTableGateway)
    {
        $this->pageTableGateway = $pageTableGateway;
    }

    /**
     * @param PageAddForm $form
     * @param $locale
     * @return Sitemap
     */
    public function saveInitForm(PageAddForm $form, $locale)
    {
        $values = $form->getData();

        $dateTime = new \DateTime();

        $sitemap = new Sitemap();
        $sitemap->setParentId((empty($values['parentId'])) ? null: $values['parentId'])
            ->setRoot((empty($values['parentId'])))
            ->setPageType($values['pageType'])
            ->setOrderNr(0)
            ->setUpdated($dateTime)
            ->setCreated($dateTime);
        $this->sitemapTableGateway->insert($sitemap);

        $this->saveRoutingInformation($sitemap);

        $page = new Page();
        $page->setLocale($locale)
            ->setSitemapId($sitemap->getId())
            ->setSlug($this->getUniqueSlugFromTitle($values['title'], $locale))
            ->setTitle($values['title'])
            ->setStatus(Page::STATUS_INACTIVE);

        $this->pageTableGateway->insert($page);

        return $sitemap;
    }

    /**
     * @param $title
     * @param $locale
     * @return mixed
     */
    protected function getUniqueSlugFromTitle($title, $locale)
    {
        $urlPathFilter = new UrlPath();
        $slug = $urlPathFilter->filter($title);

        //TODO Slug Check

        return $slug;
    }

    /**
     * @param Sitemap $sitemap
     * @throws \Exception
     */
    protected function saveRoutingInformation(Sitemap $sitemap)
    {
        $sitemap->setRoute('{slug_'.$sitemap->getId().'}/')
            ->setRouteClass($this->routeClass)
            ->setDefaultParams(json_encode($this->defaultParams));

        $this->sitemapTableGateway->update($sitemap);
    }

    /**
     * @param $id
     * @param $locale
     * @return Form
     */
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
            Page::STATUS_ACTIVE => 'Online',
            Page::STATUS_INACTIVE => 'Offline',
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

    /**
     * @param $data
     * @param $id
     * @param $locale
     * @param $approved
     * @throws \Exception
     */
    public function saveEditForm($data, $id, $locale, $approved)
    {
        $sitemap = $this->sitemapTableGateway->selectByPrimary($id);

        $result = $this->pageTableGateway->select(array(
            'sitemapId' => $id,
            'locale' => $locale
        ));
        if ($result->count() > 0) {
            $page = $result->current();
        } else {
            $page = new Page();
            $page->setSitemapId($id)
                    ->setLocale($locale);
        }

        $page->setTitle($data['page']["title"]);
        $page->setMetaDescription($data['page']["metaDescription"]);
        $page->setMetaKeywords($data['page']["metaKeywords"]);
        $page->setStatus($data['page']["status"]);

        $page->setSlug(null);
        if (strpos($sitemap->getRoute(), '{slug_') !== false) {
            $page->setSlug($this->getUniqueSlugFromTitle($data['page']["title"], $locale));
        }

        if ($page->getId() > 0) {
            $this->pageTableGateway->update($page);
        } else {
            $this->pageTableGateway->insert($page);
        }
    }
}
