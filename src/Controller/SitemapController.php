<?php
namespace Frontend42\Controller;

use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\I18n\Localization\Localization;
use Core42\View\Model\JsonModel;
use Frontend42\Command\Sitemap\AddSitemapCommand;
use Frontend42\Command\Sitemap\DeleteSitemapCommand;
use Frontend42\Command\Sitemap\ResortSitemapCommand;
use Frontend42\Form\AddPageForm;
use Frontend42\Selector\AngularSitemapSelector;
use Frontend42\Selector\AvailablePageTypesSelector;
use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;

class SitemapController extends AbstractAdminController
{
    public function indexAction()
    {
        $localization = $this->getServiceManager()->get(Localization::class);
        $defaultLocale = $localization->getDefaultLocale();

        $availableLocales = [];
        foreach ($localization->getAvailableLocalesDisplay() as $locale => $localeDisplay) {
            //TODO permission check

            $availableLocales[$locale] = $localeDisplay;
        }

        if (!in_array($defaultLocale, array_keys($availableLocales))) {
            $defaultLocale = key($availableLocales);
            reset($availableLocales);
        }

        $canAddRoutePages = (count($this->getSelector(AvailablePageTypesSelector::class)->getResult()) > 0);

        return [
            'defaultLocale' => $defaultLocale,
            'availableLocales' => $availableLocales,
            'canAddRoutePages' => $canAddRoutePages,
        ];
    }

    public function listAction()
    {
        $jsonString = $this->getRequest()->getContent();
        $options = Json::decode($jsonString, Json::TYPE_ARRAY);
        $locale = (!empty($options['locale'])) ? $options['locale'] : '';
        $result = $this
            ->getSelector(AngularSitemapSelector::class)
            ->setLocale($locale)
            ->getResult();

        return new JsonModel($result);
    }

    public function sortSaveAction()
    {
        $jsonString = $this->getRequest()->getContent();
        $sitemap = Json::decode($jsonString, Json::TYPE_ARRAY);

        $cmd = $this->getCommand(ResortSitemapCommand::class);
        $cmd->setSitemapArray($sitemap)
            ->run();

        return new JsonModel(['success' => !$cmd->hasErrors()]);
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isDelete()) {
            $deleteCmd = $this->getCommand(DeleteSitemapCommand::class);

            $deleteParams = [];
            parse_str($this->getRequest()->getContent(), $deleteParams);

            $deleteCmd->setSitemapId((int) $deleteParams['id'])
                ->run();

            return new JsonModel([
                'success' => true,
            ]);
        } elseif ($this->getRequest()->isPost()) {
            $deleteCmd = $this->getCommand(DeleteSitemapCommand::class);

            $deleteCmd->setSitemapId((int) $this->params()->fromPost('id'))
                ->run();

            return new JsonModel([
                'redirect' => $this->url()->fromRoute('admin/sitemap'),
            ]);
        }

        return new JsonModel([
            'redirect' => $this->url()->fromRoute('admin/sitemap'),
        ]);
    }

    public function addPageAction()
    {
        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        $availablePageTypeSelector = $this->getSelector(AvailablePageTypesSelector::class);

        $availablePageTypes = $availablePageTypeSelector
            ->setParentId($this->params()->fromRoute("parentId", null))
            ->getResult();

        if (empty($availablePageTypes)) {
            return $this->redirect()->toRoute('admin/sitemap');
        }

        $addPageForm = $this->getForm(AddPageForm::class);
        $addPageForm->addDefaultElements($availablePageTypes);

        if ($prg !== false) {
            /** @var AddSitemapCommand $cmd */
            $cmd = $this->getCommand(AddSitemapCommand::class);
            $cmd->setLocale($this->params()->fromRoute("locale"))
                ->setUser($this->getIdentity())
                ->setParentId($this->params()->fromRoute("parentId"));

            $formCommand = $this->getFormCommand();
            $formCommand->setForm($addPageForm)
                ->setData($prg)
                ->setCommand($cmd)
                ->run();

            if (!$formCommand->hasErrors()) {
                //ToDo Redirect
            }
        }

        return [
            'addPageForm' => $addPageForm,
        ];
    }
}
