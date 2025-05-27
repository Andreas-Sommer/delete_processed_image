<?php

use Belsignum\DeleteProcessedImage\Controller\DeleteProcessedFileController;

return [
    'delete-processed-image' => [
        'path' => '/delete-processed-image',
        'target' => DeleteProcessedFileController::class,
    ],
];