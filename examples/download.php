<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \EasyRSA\Config;
use \EasyRSA\Downloader;

$config =
    (new Config())
        ->setCerts('./easy-rsa-certs')
        ->setScripts('./easy-rsa')
        ->setArchive('./easy-rsa.tar.gz');

print_r($config);

$dnl = new Downloader($config);
$dnl->getEasyRSA();
