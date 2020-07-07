<?php

declare(strict_types=1);

namespace EasyRSA;

/**
 * @deprecated Please use Commands class instead
 * @codeCoverageIgnore
 */
class Wrapper extends Commands
{
    /**
     * @return array<string>
     */
    public function init_pki(): array
    {
        return $this->initPKI();
    }

    /**
     * @param bool $nopass
     *
     * @return array<string>
     */
    public function build_ca(bool $nopass = false): array
    {
        return $this->buildCA($nopass);
    }

    /**
     * @return array<string>
     */
    public function gen_dh(): array
    {
        return $this->genDH();
    }

    /**
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function gen_req(string $name, bool $nopass = false): array
    {
        return $this->genReq($name, $nopass);
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function sign_req_client(string $filename): array
    {
        return $this->signReqClient($filename);
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function sign_req_server(string $filename): array
    {
        return $this->signReqServer($filename);
    }

    /**
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function build_client_full(string $name, bool $nopass = false): array
    {
        return $this->buildClientFull($name, $nopass);
    }

    /**
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function build_server_full(string $name, bool $nopass = false): array
    {
        return $this->buildServerFull($name, $nopass);
    }

    /**
     * @return array<string>
     */
    public function gen_crl(): array
    {
        return $this->genCRL();
    }

    /**
     * @return array<string>
     */
    public function update_db(): array
    {
        return $this->updateDB();
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function show_req(string $filename): array
    {
        return $this->showReq($filename);
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function show_cert(string $filename): array
    {
        return $this->showCert($filename);
    }

    /**
     * @param string $filename
     * @param string $short_basename
     *
     * @return array<string>
     */
    public function import_req(string $filename, string $short_basename): array
    {
        return $this->importReq($filename, $short_basename);
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function export_p7(string $filename): array
    {
        return $this->exportP7($filename);
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function export_p12(string $filename): array
    {
        return $this->exportP12($filename);
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function set_rsa_pass(string $filename): array
    {
        return $this->setRsaPass($filename);
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function set_ec_pass(string $filename): array
    {
        return $this->setEcPass($filename);
    }
}
