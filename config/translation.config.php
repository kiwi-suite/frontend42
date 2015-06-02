<?php

return array(
    'translator' => array(
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
