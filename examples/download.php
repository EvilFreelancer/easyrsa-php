<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EasyRSA\Config;
use EasyRSA\Downloader;

$config = new Config([
    'certs'   => './easy-rsa-certs',
    'scripts' => './easy-rsa',
    'archive' => './easy-rsa.tar.gz',
]);

print_r($config);

$dnl = new Downloader($config);
$dnl->getEasyRSA();
