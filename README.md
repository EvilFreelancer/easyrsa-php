[![Latest Stable Version](https://poser.pugx.org/evilfreelancer/easyrsa-php/v/stable)](https://packagist.org/packages/evilfreelancer/easyrsa-php)
[![Total Downloads](https://poser.pugx.org/evilfreelancer/easyrsa-php/downloads)](https://packagist.org/packages/evilfreelancer/easyrsa-php)
[![Build Status](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/badges/build.png?b=master)](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/?branch=master)
[![Code Climate](https://codeclimate.com/github/EvilFreelancer/easyrsa-php/badges/gpa.svg)](https://codeclimate.com/github/EvilFreelancer/easyrsa-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/EvilFreelancer/easyrsa-php/?branch=master)
[![License](https://poser.pugx.org/evilfreelancer/easyrsa-php/license)](https://packagist.org/packages/evilfreelancer/easyrsa-php)

# EasyRSA wrapper for PHP

An easy way to use the [official EasyRSA](https://github.com/OpenVPN/easy-rsa) collection of shell
scripts in your application.

    composer require evilfreelancer/easyrsa-php

By the way, EasyRSA library support Laravel and Lumen frameworks, details [here](#frameworks-support).

## How to use

More examples you can find [here](examples).

### Download the latest release of EasyRSA

Before you start use this script need to download the [easy-rsa](https://github.com/OpenVPN/easy-rsa) package.

```php
require_once __DIR__ . '/../vendor/autoload.php';

use EasyRSA\Downloader;

$dnl = new Downloader([
    'archive' => './easy-rsa.tar.gz',
    'scripts' => './easy-rsa',
]);

$dnl->getEasyRSA();
```

Result of this script will be in `easy-rsa` folder.

### Generate certificates

```php
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
```

Result of this script will be in `easy-rsa-certs` folder.

### List of all available commands

| Method                                              | Description |
|-----------------------------------------------------|-------------|
| getContent(string $filename)                        | Show content of any certificate available in "certs" folder |
| initPKI()                                           | Instantiate Public Key Infrastructure (PKI) |
| buildCA(bool $nopass = false)                       | Build Certificate Authority (CA) |
| genDH()                                             | Generate Diffie-Hellman certificate (DH) |
| genReq()                                            | Generate request for certificate |
| signReqClient(string $filename)                     | Sign request for client certificate |
| signReqServer(string $filename)                     | Sign request for server certificate |
| buildClientFull(string $name, bool $nopass = false) | Build public and private key of client |
| buildServerFull(string $name, bool $nopass = false) | Build public and private key of server |
| revoke(string $filename)                            | Revoke certificate |
| genCRL()                                            | Generate Certificate Revocation List (CRL) |
| updateDB()                                          | Update certificates database |
| showCert(string $filename)                          | Display information about certificate |
| showReq(string $filename)                           | Display information about request |
| importReq(string $filename)                         | Import request |
| exportP7(string $filename)                          | Export file in format of Public-Key Cryptography Standards (PKCS) v7 (P7) |
| exportP12(string $filename)                         | Export file in format of Public-Key Cryptography Standards (PKCS) v12 (P12) |
| setRSAPass(string $filename)                        | Set password in Rivest–Shamir–Adleman (RSA) format |
| setECPass(string $filename)                         | Set password in Elliptic Curve (EC) format |

You also can read content of generated certificate via `getConfig($filename)` method:

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \EasyRSA\Commands;

$cmd = new Commands([
    'scripts' => './easy-rsa',
    'certs'   => './easy-rsa-certs',
]);

$file = $cmd->getContent('ca.crt');
echo "$file\n";

$file = $cmd->getContent('server.crt');
echo "$file\n";

$file = $cmd->getContent('server.key');
echo "$file\n";
```

## Environment variables

You can set these variables via environment on host system or with help
of [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) library or via
any other way which you like.

```dotenv
EASYRSA_DN="cn_only"
#EASYRSA_DN="org"
EASYRSA_REQ_COUNTRY="DE"
EASYRSA_REQ_PROVINCE="California"
EASYRSA_REQ_CITY="San Francisco"
EASYRSA_REQ_ORG="Copyleft Certificate Co"
EASYRSA_REQ_EMAIL="me@example.net"
EASYRSA_REQ_OU="My Organizational Unit"
EASYRSA_REQ_CN="ChangeMe"
EASYRSA_KEY_SIZE=2048
EASYRSA_ALGO=rsa
EASYRSA_CA_EXPIRE=3650
EASYRSA_CERT_EXPIRE=3650
EASYRSA_DIGEST="sha256"
```

Example of environment variables configuration which should be used on certificate
build stage can be fond [here](vars.example). 

## Frameworks support

### Laravel

The package's service provider will automatically register its service provider.

Publish the `easy-rsa.php` configuration file:

```sh
php artisan vendor:publish --provider="EasyRSA\Laravel\ServiceProvider"
```

#### Alternative configuration method via .env file

After you publish the configuration file as suggested above, you may configure library
by adding the following to your application's `.env` file (with appropriate values):
  
```ini
EASYRSA_WORKER=default
EASYRSA_ARCHIVE=./easy-rsa.tar.gz
EASYRSA_SCRIPTS=./easy-rsa
EASYRSA_CERTS=./easy-rsa-certs
```

### Lumen

If you work with Lumen, please register the service provider and configuration in `bootstrap/app.php`:

```php
$app->register(EasyRSA\Laravel\ServiceProvider::class);
$app->configure('easy-rsa');
```

Manually copy the configuration file to your application.

## Testing

This library can tested in multiple different ways

```shell script
composer test:lint
composer test:types
composer test:unit
```

or just in one command

```shell script
composer test
```

## Links

* https://github.com/RMerl/asuswrt-merlin.ng/wiki/Generating-OpenVPN-keys-using-Easy-RSA
* https://github.com/OpenVPN/easy-rsa
