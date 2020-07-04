[![Latest Stable Version](https://poser.pugx.org/evilfreelancer/easyrsa-php/v/stable)](https://packagist.org/packages/evilfreelancer/easyrsa-php)
[![Total Downloads](https://poser.pugx.org/evilfreelancer/easyrsa-php/downloads)](https://packagist.org/packages/evilfreelancer/easyrsa-php)
[![Build Status](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/badges/build.png?b=master)](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/?branch=master)
[![Code Climate](https://codeclimate.com/github/EvilFreelancer/easyrsa-php/badges/gpa.svg)](https://codeclimate.com/github/EvilFreelancer/easyrsa-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/?branch=master)
[![License](https://poser.pugx.org/evilfreelancer/easyrsa-php/license)](https://packagist.org/packages/evilfreelancer/easyrsa-php)

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
        ->set('archive', './easy-rsa.tar.gz')
        ->set('scripts', './easy-rsa')
        ->set('certs', './easy-rsa-certs');

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
        ->set('scripts', './easy-rsa')
        ->set('certs', './easy-rsa-certs');

$wrp = new Wrapper($config);
$wrp->init_pki();
$wrp->build_ca(true);
$wrp->gen_dh();
$wrp->build_server_full('server', true);
$wrp->build_client_full('client1', true);
$wrp->build_client_full('client2', true);
```

Result of this script will be in `easy-rsa-certs` folder.

## List of all `Wrapper` methods

Main methods

* init_pki()
* build_ca(bool $nopass = false)
* gen_dh()
* gen_req(string $name, bool $nopass = false)
* sign_req_client(string $filename)
* sign_req_server(string $filename)
* build_client_full(string $name, bool $nopass = false)
* build_server_full(string $name, bool $nopass = false)
* revoke(string $filename)
* gen_crl()
* update_db()
* show_req(string $filename)
* show_cert(string $filename)
* import_req(string $filename)
* export_p7(string $filename)
* export_p12(string $filename)
* set_rsa_pass(string $filename)
* set_ec_pass(string $filename)

Optional method

* getContent() - Show content of any certificates what was created

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \EasyRSA\Config;
use \EasyRSA\Wrapper;

$config =
    (new Config())
        ->set('scripts', './easy-rsa')
        ->set('certs', './easy-rsa-certs');

$wrp = new Wrapper($config);

$file = $wrp->getContent('ca.crt');
echo "$file\n";

$file = $wrp->getContent('server.crt');
echo "$file\n";

$file = $wrp->getContent('server.key');
echo "$file\n";
```

## Links

* https://github.com/RMerl/asuswrt-merlin/wiki/Generating-OpenVPN-keys-using-Easy-RSA
* https://github.com/OpenVPN/easy-rsa
