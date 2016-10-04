<?php
namespace Frontend42;

use Frontend42\FormElements\Block;
use Frontend42\FormElements\Service\BlockFactory;
use Frontend42\FormElements\Service\PageTypeSelectorFactory;

return [
    'form_elements' => [
        'factories' => [
            'pageTypeSelector'          => PageTypeSelectorFactory::class,
            Block::class                => BlockFactory::class,
        ],
        'aliases' => [
            'block'                     => Block::class,
        ],
    ],
];
