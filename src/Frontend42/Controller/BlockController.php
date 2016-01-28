<?php
namespace Frontend42\Controller;

use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\View\Model\JsonModel;
use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Zend\Json\Json;

class BlockController extends AbstractAdminController
{
    /**
     * @return array|JsonModel
     * @throws \Exception
     */
    public function saveInheritanceAction()
    {
        $this->getCommand('Frontend42\Block\SaveInheritance')
            ->setSourcePageId($this->params()->fromPost("sourcePageId"))
            ->setTargetPageId($this->params()->fromPost("targetPageId"))
            ->setSection($this->params()->fromPost("section"))
            ->run();

        $result = $this->getTableGateway('Frontend42\BlockInheritance')->select([
            'sourcePageId' => $this->params()->fromPost("sourcePageId"),
            'section' => $this->params()->fromPost("section")
        ]);

        if ($result->count() == 0) {
            return [];
        }

        $blockInheritance = $result->current();

        $page = $this->getTableGateway('Frontend42\Page')->selectByPrimary($blockInheritance->getTargetPageId());

        return new JsonModel([
            'id' => $page->getId(),
            'name' => $page->getName(),
        ]);
    }

    /**
     * @return JsonModel
     */
    public function cleanInheritanceAction()
    {
        $this->getCommand('Frontend42\Block\CleanInheritance')
            ->setSourcePageId($this->params()->fromPost("sourcePageId"))
            ->setSection($this->params()->fromPost("section"))
            ->run();

        return new JsonModel();
    }

    /**
     * @return JsonModel
     * @throws \Exception
     */
    public function listInheritancePageAction()
    {
        $jsonString = $this->getRequest()->getContent();
        $options = Json::decode($jsonString, Json::TYPE_ARRAY);

        $currentPage = $this->getTableGateway('Frontend42\Page')->selectByPrimary((int) $options['pageId']);
        if (empty($currentPage)) {
            return new JsonModel();
        }

        $sitemap = $this->getTableGateway('Frontend42\Sitemap')->selectByPrimary($currentPage->getSitemapId());

        $result = $this->getSelector('Frontend42\Sitemap')
            ->setLocale($currentPage->getLocale())
            ->getResult();

        return new JsonModel($this->prepareJsonTree($result, $sitemap, $currentPage, $options['section']));
    }

    /**
     * @param $items
     * @param Sitemap $sitemap
     * @param Page $page
     * @param $section
     * @return array
     */
    protected function prepareJsonTree($items, Sitemap $sitemap, Page $page, $section)
    {
        $tree = [];
        foreach ($items as $_item) {
            $selectable = true;

            if ($page->getId() == $_item['page']->getId()) {
                $selectable = false;
            } elseif ($sitemap->getId() === $_item['sitemap']->getId()) {

            } else {
                $pageTypeOptions = $this->getServiceLocator()
                    ->get('Frontend42\PageTypeProvider')
                    ->getPageTypeOptions($_item['sitemap']->getPageType());

                $pageTypeElements = $pageTypeOptions->getElements();
                $found = false;
                foreach ($pageTypeElements as $_element) {
                    if ($_element['name'] == $section && $_element['type'] == 'block') {
                        $found = true;

                        break;
                    }
                }

                $selectable = $found;
            }
            $node = [
                'id'        => $_item['sitemap']->getId(),
                'pageId'    => $_item['page']->getId(),
                'title'     => $_item['page']->getName(),
                'pageType'  => $_item['sitemap']->getPageType(),
                'items'     => [],
                'selectable'=> $selectable,
            ];
            if (!empty($_item['children']) && !$_item['sitemap']->getTerminal()) {
                $node['items'] = $this->prepareJsonTree($_item['children'], $sitemap, $page, $section);
            }

            $tree[] = $node;
        }

        return $tree;
    }
}
