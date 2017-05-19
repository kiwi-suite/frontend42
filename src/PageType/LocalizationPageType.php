<?php
namespace Frontend42\PageType;

use Core42\I18n\Localization\Localization;
use Frontend42\Controller\Frontend\DefaultController;
use Frontend42\Model\Page;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

class LocalizationPageType extends AbstractPageType
{

    /**
     * @var string
     */
    protected $controller = DefaultController::class;

    /**
     * @var string
     */
    protected $action = "localization";

    /**
     * @var Localization
     */
    protected $localization;

    /**
     * @var string
     */
    protected $view;

    /**
     * LocalizationPageType constructor.
     * @param Localization $localization
     */
    public function __construct(Localization $localization)
    {
        $this->localization = $localization;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @param Page $page
     * @return array
     */
    public function getRouting(Page $page)
    {
        $localizationPart = \Locale::getPrimaryLanguage($page->getLocale());
        $localizationPartName = 'language';
        $localizationCondition = array_map(function ($value){
            return \Locale::getPrimaryLanguage($value);
        }, [$this->localization->getDefaultLocale()]);

        if ($this->localization->getType() == Localization::TYPE_REGION) {
            $localizationPart = $page->getLocale();
            $localizationPartName = 'locale';
            $localizationCondition = [$this->localization->getDefaultLocale()];
        }

        $routing = [
            'type' => Literal::class,
            'options' => [
                'route' => '/' . $localizationPart . '/',
                'defaults' => [
                    'controller' => $this->getController(),
                    'action' => $this->getAction(),
                    'pageId' => $page->getId(),
                    $localizationPartName => $localizationPart,
                ],
            ],
        ];

        if ($page->getLocale() == $this->localization->getDefaultLocale()) {
            $routing['type'] = Segment::class;
            $routing['options']['route'] = '/[:'.$localizationPartName.'/]';
            $routing['options']['constraints'] = [
                $localizationPartName => '('.implode("|", $localizationCondition).')'
            ];
        }

        return $routing;
    }
}
