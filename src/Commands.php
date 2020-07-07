<?php

declare(strict_types=1);

namespace EasyRSA;

use EasyRSA\Interfaces\CommandsInterface;

class Commands extends Worker implements CommandsInterface
{
    /**
     * If need to make certificate without password
     */
    protected const NOPASS = 'nopass';

    /**
     * Instantiate Public Key Infrastructure (PKI)
     *
     * @return array<string>
     */
    public function initPKI(): array
    {
        return $this->exec('init-pki');
    }

    /**
     * Build Certificate Authority (CA)
     *
     * @param bool $nopass
     *
     * @return array<string>
     */
    public function buildCA(bool $nopass = false): array
    {
        $param = $nopass ? self::NOPASS : '';

        return $this->exec("build-ca $param");
    }

    /**
     * Generate Diffie-Hellman certificate (DH)
     *
     * @return array<string>
     */
    public function genDH(): array
    {
        return $this->exec('gen-dh');
    }

    /**
     * Generate request for certificate
     *
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function genReq(string $name, bool $nopass = false): array
    {
        $param = $nopass ? self::NOPASS : '';

        return $this->exec("gen-req $name $param");
    }

    /**
     * Sign request for client certificate
     *
     * @param string $filename
     *
     * @return array<string>
     */
    public function signReqClient(string $filename): array
    {
        return $this->exec("sign-req client $filename");
    }

    /**
     * Sign request for server certificate
     *
     * @param string $filename
     *
     * @return array<string>
     */
    public function signReqServer(string $filename): array
    {
        return $this->exec("sign-req server $filename");
    }

    /**
     * Build public and private key of client
     *
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function buildClientFull(string $name, bool $nopass = false): array
    {
        $param = $nopass ? self::NOPASS : '';

        return $this->exec("build-client-full $name $param");
    }

    /**
     * Build public and private key of client
     *
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function buildServerFull(string $name, bool $nopass = false): array
    {
        $param = $nopass ? self::NOPASS : '';

        return $this->exec("build-server-full $name $param");
    }

    /**
     * Revoke certificate
     *
     * @param string $filename
     *
     * @return array<string>
     */
    public function revoke(string $filename): array
    {
        return $this->exec("revoke $filename");
    }

    /**
     * Generate Certificate Revocation List (CRL)
     *
     * @return array<string>
     */
    public function genCRL(): array
    {
        return $this->exec('gen-crl');
    }

    /**
     * Update certificates database
     *
     * @return array<string>
     */
    public function updateDB(): array
    {
        return $this->exec('update-db');
    }

    /**
     * Display information about certificate
     *
     * @param string $filename
     *
     * @return array<string>
     */
    public function showCert(string $filename): array
    {
        return $this->exec("show-cert $filename");
    }

    /**
     * Display information about request
     *
     * @param string $filename
     *
     * @return array<string>
     */
    public function showReq(string $filename): array
    {
        return $this->exec("show-req $filename");
    }

    /**
     * Import request
     *
     * @param string $filename
     * @param string $short_basename
     *
     * @return array<string>
     */
    public function importReq(string $filename, string $short_basename): array
    {
        return $this->exec("import-req $filename $short_basename");
    }

    /**
     * Export file in format of Public-Key Cryptography Standards (PKCS) v7 (P7)
     *
     * @param string $filename
     *
     * @return array<string>
     */
    public function exportP7(string $filename): array
    {
        return $this->exec("export-p7 $filename");
    }

    /**
     * Export file in format of Public-Key Cryptography Standards (PKCS) v12 (P12)
     *
     * @param string $filename
     *
     * @return array<string>
     */
    public function exportP12(string $filename): array
    {
        return $this->exec("export-p12 $filename");
    }

    /**
     * Set password in Rivest–Shamir–Adleman (RSA) format
     *
     * @param string $filename
     *
     * @return array<string>
     */
    public function setRSAPass(string $filename): array
    {
        return $this->exec("set-rsa-pass $filename");
    }

    /**
     * Set password in Elliptic Curve (EC) format
     *
     * @param string $filename
     *
     * @return array<string>
     */
    public function setECPass(string $filename): array
    {
        return $this->exec("set-ec-pass $filename");
    }
}
