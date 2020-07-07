<?php

return [

    // Worker which will be used by default
    'defaultWorker' => env('EASYRSA_WORKER', 'default'),

    // List of available workers
    'workers'       => [

        // Settigs of default worker
        'default' => [
            'archive' => 'easy-rsa.tar.gz', // Path to archive with EasyRSA scripts
            'scripts' => 'easy-rsa',        // Path to folder with EasyRSA scripts
            'certs'   => 'easy-rsa-certs',  // Part to certificates store folder
        ],
    ],
];
