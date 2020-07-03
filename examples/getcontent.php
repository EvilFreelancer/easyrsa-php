<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EasyRSA\Config;
use EasyRSA\Wrapper;

$config =
    (new Config())
        ->setCerts('./easy-rsa-certs')
        ->setScripts('./easy-rsa')
        ->setArchive('./easy-rsa.tar.gz');

$wrp = new Wrapper($config);

$file = $wrp->getContent('ca.crt');
echo "$file\n";

$file = $wrp->getContent('server.crt');
echo "$file\n";

$file = $wrp->getContent('server.key');
echo "$file\n";
