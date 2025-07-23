<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Delete Processed Image Files',
    'description' => 'This TYPO3 extension provides a backend tool to remove all processed (cropped, scaled, etc.) variants of a FAL image.
It adds a contextual “Delete Processed Files” button directly in the Filelist module, enabling editors and integrators to manually clear processed images for individual file references when needed.',
    'category' => 'backend',
    'author' => 'Belsignum',
    'author_email' => 'dev@belsignum.de',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '13.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
