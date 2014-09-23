<?php

return array(
    'translation_manager' => array(
        'factories' => array(
            'Frontend42\Router' => 'Frontend42\I18n\Translator\Service\RouteLoaderFactory',
        ),
    ),

    'translator' => array(
        'remote_translation' => array(
            array(
                'type' => 'Frontend42\Router',
                'text_domain' => 'router',
            ),
        ),
        'translation_file_patterns' => array(
            array(
                'type' => 'phparray',
                'base_dir' => __DIR__ . '/../data/language/',
                'pattern' => '%s.php',
                'text_domain' => 'admin',
            ),
        ),
    ),
);
