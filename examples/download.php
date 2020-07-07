<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EasyRSA\Downloader;

$dnl = new Downloader([
    'archive' => './easy-rsa.tar.gz',
    'scripts' => './easy-rsa',
]);
$dnl->getEasyRSA();
