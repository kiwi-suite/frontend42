<?php
namespace Frontend42\PageType;

use Frontend42\Controller\Frontend\DefaultController;
use Frontend42\Model\Page;
use Zend\Router\Http\Literal;

class DefaultPageType extends AbstractPageType
{
    /**
     * @var string
     */
    protected $controller = DefaultController::class;

    /**
     * @var string
     */
    protected $action = "content";

    /**
     * @var string
     */
    protected $view;

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
     * @return array|false
     */
    public function getRouting(Page $page)
    {
        if (strlen($page->getSlug()) == 0) {
            return false;
        }

        return [
            'type' => Literal::class,
            'options' => [
                'route' => $page->getSlug() . '/',
                'defaults' => [
                    'controller' => $this->getController(),
                    'action' => $this->getAction(),
                    'pageId' => $page->getId(),
                ],
            ],
        ];
    }
}
