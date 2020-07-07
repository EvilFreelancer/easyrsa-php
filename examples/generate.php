<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use EasyRSA\Commands;

// Load dotenv?
if (file_exists(__DIR__ . '/../vars.example')) {
    Dotenv::createImmutable(__DIR__ . '/../', 'vars.example')->load();
}

$cmd = new Commands([
    'scripts' => './easy-rsa',
    'certs'   => './easy-rsa-certs',
]);

$cmd->initPKI();
$cmd->buildCA(true);
$cmd->genDH();
$cmd->buildServerFull('server', true);
$cmd->buildClientFull('client1', true);
$cmd->buildClientFull('client2', true);
