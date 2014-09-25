<?php
namespace Frontend42\PageType;

use Core42\Form\Service\FormPluginManager;
use Frontend42\Model\Content;
use Frontend42\Model\PageVersion;
use Frontend42\TableGateway\ContentTableGateway;
use Frontend42\TableGateway\PageVersionTableGateway;
use Zend\Db\Sql\Select;
use Zend\Form\Form;

class ContentPageType extends AbstractPageType
{
    /**
     * @var ContentTableGateway
     */
    protected $contentTableGateway;

    /**
     * @var PageVersionTableGateway
     */
    protected $pageVersionTableGateway;

    /**
     * @var FormPluginManager
     */
    protected $formPluginManager;

    protected $defaultParams = array(
        'controller' => 'Portal\Content',
        'action' => 'index',
    );

    /**
     * @param PageVersionTableGateway $pageVersionTableGateway
     * @param ContentTableGateway $contentTableGateway
     * @param FormPluginManager $formPluginManager
     */
    public function __construct(
        PageVersionTableGateway $pageVersionTableGateway,
        ContentTableGateway $contentTableGateway,
        FormPluginManager $formPluginManager
    ) {
        $this->contentTableGateway = $contentTableGateway;

        $this->pageVersionTableGateway = $pageVersionTableGateway;

        $this->formPluginManager = $formPluginManager;
    }

    public function getEditForm($id, $locale)
    {
        $form = parent::getEditForm($id, $locale);

        $fieldset = new Form("elementFields");
        $fieldset->setWrapElements(true);

        $result = $this->pageTableGateway->select(array(
            'sitemapId' => $id,
            'locale' => $locale
        ));

        if ($result->count() > 0) {
            $orignalContentForm = null;
            foreach ($this->formPluginManager->get('Frontend42\Content') as $tmp) {
                $orignalContentForm = $tmp;
                break;
            }

            $page = $result->current();

            $pageVersion = $this->pageVersionTableGateway->select(function (Select $select) use($page){
                $select->where(array('pageId' => $page->getId()));
                $select->order("created DESC, id DESC");
                $select->limit(1);
            });

            if ($pageVersion->count() > 0) {
                $pageVersion = $pageVersion->current();

                $result = $this->contentTableGateway->select(function (Select $select) use ($pageVersion){
                    $select->where(array('versionId' => $pageVersion->getId()));
                    $select->order("orderNr ASC");
                });
                foreach ($result as $content) {
                    $contentForm = clone $orignalContentForm;
                    $contentForm->setName(uniqid());
                    $contentForm->setData(json_decode($content->getContent(), true));

                    $fieldset->add($contentForm);
                }
            }


        }

        $form->add($fieldset);

        return $form;
    }

    public function saveEditForm($data, $id, $locale)
    {
        parent::saveEditForm($data, $id, $locale);

        $page = $this->pageTableGateway->select(array(
            'sitemapId' => $id,
            'locale' => $locale
        ))->current();

        $this->pageVersionTableGateway->update(array(
            'approved' => false,
        ), array(
            'pageId' => $page->getId()
        ));

        $dateTime = new \DateTime();

        $pageVersion = new PageVersion();
        $pageVersion->setPageId($page->getId())
            ->setApproved(true)
            ->setCreated($dateTime);
        $this->pageVersionTableGateway->insert($pageVersion);

        if (!empty($data['elementFields'])) {
            $order = 1;
            foreach ($data['elementFields'] as $element) {
                if ($element['delete'] == "1") {
                    continue;
                }

                $content = new Content();
                $content->setVersionId($pageVersion->getId());
                $content->setCreated($dateTime);
                $content->setFormType("");
                $content->setOrderNr($order);

                $payload = json_encode(array(
                    'subtitle' => $element['subtitle'],
                    'text' => $element['text'],
                ));
                $content->setContent($payload);

                $this->contentTableGateway->insert($content);

                $order++;
            }
        }
    }
}
