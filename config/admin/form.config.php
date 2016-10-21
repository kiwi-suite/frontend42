<?php
namespace Frontend42;

use Frontend42\FormElements\Block;
use Frontend42\FormElements\Service\BlockFactory;
use Frontend42\FormElements\Service\OnlineSwitcherFactory;

return [
    'form_elements' => [
        'factories' => [
            'onlineSwitcher'            => OnlineSwitcherFactory::class,
            Block::class                => BlockFactory::class,
        ],
        'aliases' => [
            'block'                     => Block::class,
        ],
    ],
];
