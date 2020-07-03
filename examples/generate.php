<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use EasyRSA\Config;
use EasyRSA\Wrapper;

// Load dotenv?
if (file_exists(__DIR__ . '/vars.example')) {
    Dotenv::createImmutable(__DIR__, 'vars.example')->load();
}

$config =
    (new Config())
        ->setCerts('./easy-rsa-certs')
        ->setScripts('./easy-rsa')
        ->setArchive('./easy-rsa.tar.gz');

print_r($config);

$wrp = new Wrapper($config);
$wrp->init_pki();
$wrp->build_ca(true);
$wrp->gen_dh();
$wrp->build_server_full('server', true);
$wrp->build_client_full('client1', true);
$wrp->build_client_full('client2', true);
