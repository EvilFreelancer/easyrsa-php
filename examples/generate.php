<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EasyRSA\Commands;

$cmd = new Commands([
    'scripts' => './easy-rsa',
    'certs'   => './easy-rsa-certs',
]);

// Load EASYRSA_* variables from a vars file into the subprocess environment.
// Dotenv::load() alone is not enough: it does not export values to child
// processes, so the spawned easyrsa script would not see them.
if (file_exists(__DIR__ . '/../vars.example')) {
    $cmd->loadVars(__DIR__ . '/../vars.example');
}

$cmd->initPKI();
$cmd->buildCA(true);
$cmd->genDH();
$cmd->buildServerFull('server', true);
$cmd->buildClientFull('client1', true);
$cmd->buildClientFull('client2', true);
