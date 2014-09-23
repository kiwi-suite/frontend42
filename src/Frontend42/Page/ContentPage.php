<?php
namespace Frontend42\Page;

use Frontend42\Form\ContentForm;
use Frontend42\Model\Content;
use Frontend42\TableGateway\ContentTableGateway;
use Frontend42\TableGateway\TreeLanguageTableGateway;
use Frontend42\TableGateway\TreeTableGateway;
use Zend\Form\Form;

class ContentPage extends AbstractPage
{
    protected $contentTableGateway;

    /**
     * @var ContentForm
     */
    protected $contentForm;

    protected $defaultParams = array(
        'controller' => 'Portal\Content',
        'action' => 'index',
    );

    /**
     * @param TreeTableGateway $treeTableGateway
     * @param TreeLanguageTableGateway $treeLanguageTableGateway
     * @param ContentTableGateway $contentTableGateway
     */
    public function __construct(TreeTableGateway $treeTableGateway, TreeLanguageTableGateway $treeLanguageTableGateway, ContentTableGateway $contentTableGateway, $contentForm)
    {
        parent::__construct($treeTableGateway, $treeLanguageTableGateway);

        $this->contentTableGateway = $contentTableGateway;

        $this->contentForm = $contentForm;
    }

    public function getEditForm($id, $locale)
    {
        $form = parent::getEditForm($id, $locale);

        $fieldset = new Form("elementFields");
        $fieldset->setWrapElements(true);

        $result = $this->treeLanguageTableGateway->select(array(
            'treeId' => $id,
            'locale' => $locale
        ));

        if ($result->count() > 0) {
            $orignalContentForm = null;
            foreach ($this->contentForm as $tmp) {
                $orignalContentForm = $tmp;
                break;
            }

            $treeLanguage = $result->current();

            $result = $this->contentTableGateway->select(array('treeLanguageId' => $treeLanguage->getId()));
            foreach ($result as $content) {
                $contentForm = clone $orignalContentForm;
                $contentForm->setName(uniqid());
                $contentForm->setData(json_decode($content->getContent(), true));

                $fieldset->add($contentForm);
            }
        }

        $form->add($fieldset);

        return $form;
    }

    public function saveEditForm($data, $id, $locale)
    {
        parent::saveEditForm($data, $id, $locale);

        $treeLanguage = $this->treeLanguageTableGateway->select(array(
            'treeId' => $id,
            'locale' => $locale
        ))->current();

        $this->contentTableGateway->delete(array(
            'treeLanguageId' => $treeLanguage->getId(),
        ));

        if (!empty($data['elementFields'])) {
            $order = 1;
            foreach ($data['elementFields'] as $element) {
                if ($element['delete'] == "1") {
                    continue;
                }

                $content = new Content();
                $content->setTreeLanguageId($treeLanguage->getId());
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
