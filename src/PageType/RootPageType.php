<?php
namespace Frontend42\PageType;

use Frontend42\Model\Page;
use Zend\Router\Http\Literal;

class RootPageType extends AbstractPageType
{
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
     * @return array
     */
    public function getRouting(Page $page)
    {
        return [
            'type' => Literal::class,
            'options' => [
                'route' => '/',
                'defaults' => [
                    'controller' => $this->getController(),
                    'action' => $this->getAction(),
                    'pageId' => $page->getId(),
                ],
            ],
        ];
    }
}
