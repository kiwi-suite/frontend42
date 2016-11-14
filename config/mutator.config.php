<?php
namespace Frontend42;

use Frontend42\Mutator\Strategy\BlockStrategy;
use Frontend42\Mutator\Strategy\Service\BlockStrategyFactory;

return [
    'mutator' => [
        'factories' => [
            BlockStrategy::class                    => BlockStrategyFactory::class,
        ],
        'aliases' => [
            'block'                                 => BlockStrategy::class,
        ],
    ],
];
