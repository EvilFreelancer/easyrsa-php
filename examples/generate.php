<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Load dotenv?
if (file_exists(__DIR__ . '/vars.example')) {
    (new Dotenv\Dotenv(__DIR__, 'vars.example'))->load();
}

use \EasyRSA\Config;
use \EasyRSA\Wrapper;

$config =
    (new Config())
        ->setFolder('./easy-rsa')
        ->setArchive('./easy-rsa.tar.gz');

$wrp = new Wrapper($config);
$wrp->init_pki();
$wrp->build_ca(true);
$wrp->gen_dh();
$wrp->build_server_full('server', true);
$wrp->build_client_full('client1', true);
$wrp->build_client_full('client2', true);
