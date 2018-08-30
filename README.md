[![Latest Stable Version](https://poser.pugx.org/evilfreelancer/easyrsa-php/v/stable)](https://packagist.org/packages/evilfreelancer/easyrsa-php)
[![Build Status](https://travis-ci.org/EvilFreelancer/easyrsa-php.svg?branch=master)](https://travis-ci.org/EvilFreelancer/easyrsa-php)
[![Total Downloads](https://poser.pugx.org/evilfreelancer/easyrsa-php/downloads)](https://packagist.org/packages/evilfreelancer/easyrsa-php)
[![License](https://poser.pugx.org/evilfreelancer/easyrsa-php/license)](https://packagist.org/packages/evilfreelancer/easyrsa-php)
[![PHP 7 ready](https://php7ready.timesplinter.ch/EvilFreelancer/easyrsa-php/master/badge.svg)](https://travis-ci.org/EvilFreelancer/easyrsa-php)
[![Code Climate](https://codeclimate.com/github/EvilFreelancer/easyrsa-php/badges/gpa.svg)](https://codeclimate.com/github/EvilFreelancer/easyrsa-php)
[![Scrutinizer CQ](https://scrutinizer-ci.com/g/evilfreelancer/easyrsa-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/evilfreelancer/easyrsa-php/)

# EasyRSA wrapper for PHP

    composer require evilfreelancer/easyrsa-php

## How to use

More examples you can find [here](examples).

### Download latest release of EasyRSA

Before you start use this script need to download the `easyrsa` package.

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \EasyRSA\Config;
use \EasyRSA\Downloader;

$config =
    (new Config())
        ->setFolder('./easy-rsa')
        ->setCertsFolder('.')
        ->setArchive('./easy-rsa.tar.gz');

$dnl = new Downloader($config);
$dnl->getEasyRSA();
```

Result of this script will be in `easy-rsa` folder.

### Generate certificates

```php
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
```

Result of this script will be in `pki` folder.

## List of all `Wrapper` methods

    init_pki()
    build_ca(bool $nopass = false)
    build_client_full(string $name, bool $nopass = false)
    build_server_full(string $name, bool $nopass = false)
    gen_req(string $name, bool $nopass = false)
    import_req(string $filename)
    show_req(string $filename)
    sign_req_client(string $filename)
    sign_req_server(string $filename)
    gen_dh()

## Links

* https://github.com/RMerl/asuswrt-merlin/wiki/Generating-OpenVPN-keys-using-Easy-RSA
* https://github.com/OpenVPN/easy-rsa
