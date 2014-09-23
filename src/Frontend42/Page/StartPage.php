<?php
namespace Frontend42\Page;

use Frontend42\Model\Content;
use Frontend42\Model\TreeLanguage;

class StartPage extends ContentPage
{
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
