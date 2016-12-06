<?php
namespace Frontend42\Block;

use Frontend42\Block\Service\BlockPluginManager;
use Frontend42\Model\Block;
use Frontend42\View\Model\BlockModel;
use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\PhpRenderer;

class BlockContainer
{
    /**
     * @var Block[]
     */
    protected $blocks = [];

    /**
     * @var BlockPluginManager
     */
    protected $blockPluginManager;

    /**
     * @var PhpRenderer
     */
    protected $renderer;

    /**
     * BlockContainer constructor.
     * @param BlockPluginManager $blockPluginManager
     * @param PhpRenderer $renderer
     */
    public function __construct(BlockPluginManager $blockPluginManager, PhpRenderer $renderer)
    {
        $this->blockPluginManager = $blockPluginManager;
        $this->renderer = $renderer;
    }

    /**
     * @param array $blocks
     * @return $this
     */
    public function setBlocks(array $blocks)
    {
        $this->blocks = $blocks;

        return $this;
    }

    public function render()
    {
        $html = "";

        foreach ($this->blocks as $blockModel) {
            if (!$this->blockPluginManager->has($blockModel->getType())) {
                continue;
            }

            /** @var BlockInterface $blockType */
            $blockType = $this->blockPluginManager->get($blockModel->getType());

            $viewModel = $blockType->getViewModel($blockModel->getElements());

            if ($viewModel instanceof ModelInterface) {
                $html .= $this->renderer->render($viewModel) . PHP_EOL;

                continue;
            }

            $viewModel = new BlockModel($viewModel);
            $viewModel->setTemplate("block/" . strtolower($blockModel->getType()));

            $html .= $this->renderer->render($viewModel) . PHP_EOL;
        }

        return $html;
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
