<?php
namespace Frontend42\Page;

use Frontend42\Model\TreeLanguage;

class LockedPage extends AbstractPage
{
    protected $defaultParams = array(
        'controller' => 'Frontend42\Content',
        'action' => 'index',
    );

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

        if ($treeLanguage->getId() > 0) {
            $this->treeLanguageTableGateway->update($treeLanguage);
        } else {
            $this->treeLanguageTableGateway->insert($treeLanguage);
        }
    }
}
