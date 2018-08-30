<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \EasyRSA\Config;
use \EasyRSA\Downloader;

$config =
    (new Config())
        ->setFolder('./easy-rsa')
        ->setArchive('./easy-rsa.tar.gz');

$dnl = new Downloader($config);
$dnl->getEasyRSA();
