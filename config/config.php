<?php
/**
 * La configuration, pas besoin de fichiers, plus rapi
 * @var array $config
 */
$config = [
    'routes' => [
        'merge' => [
            'description' => 'Merge multiple markdown files in one',
            'controller'  => '\\MdTools\\Command\\Markdown\\Merge'
        ]
    ]
];

return $config;