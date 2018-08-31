<?php

namespace EasyRSA;

class Wrapper
{
    /**
     * Main location of easyrsa scripts
     * @var string
     */
    private $_scripts;

    /**
     * Path to folder with certificates
     * @var string
     */
    private $_certs;

    /**
     * Wrapper constructor, need configuration for normal usage
     *
     * @param   Config $config
     * @throws  \RuntimeException
     */
    public function __construct(Config $config)
    {
        // Create folders for certificates
        $this->_certs = $config->getCerts();
        putenv("EASYRSA_PKI={$this->_certs}");
        if (@mkdir($this->_certs, 0755, true) || is_dir($this->_certs)) {
            error_log("Folder '{$this->_certs}' created");
        } else {
            throw new \RuntimeException("Folder {$this->_certs} can't be created");
        }

        $this->_scripts = $config->getScripts();
    }

    /**
     * Execute some command and return result
     *
     * @param   string $cmd
     * @return  array
     */
    private function exec(string $cmd): array
    {
        chdir($this->_certs);
        exec($this->_scripts . '/easyrsa3/easyrsa --batch ' . $cmd, $result);
        return $result;
    }

    public function init_pki(): array
    {
        return $this->exec('init-pki');
    }

    public function build_ca(bool $nopass = false): array
    {
        $param = $nopass ? 'nopass' : '';
        return $this->exec("build-ca $param");
    }

    public function gen_dh(): array
    {
        return $this->exec('gen-dh');
    }

    public function gen_req(string $name, bool $nopass = false): array
    {
        $param = $nopass ? 'nopass' : '';
        return $this->exec("gen-req $name $param");
    }

    public function sign_req_client(string $filename): array
    {
        return $this->exec("sign-req server $filename");
    }

    public function sign_req_server(string $filename): array
    {
        return $this->exec("sign-req client $filename");
    }

    public function build_client_full(string $name, bool $nopass = false): array
    {
        $param = $nopass ? 'nopass' : '';
        return $this->exec("build-client-full $name $param");
    }

    public function build_server_full(string $name, bool $nopass = false): array
    {
        $param = $nopass ? 'nopass' : '';
        return $this->exec("build-server-full $name $param");
    }

    public function revoke(string $filename): array
    {
        return $this->exec("revoke $filename");
    }

    public function gen_crl(): array
    {
        return $this->exec('gen-crl');
    }

    public function update_db(): array
    {
        return $this->exec('update-db');
    }

    public function show_req(string $filename): array
    {
        return $this->exec("show-req $filename");
    }

    public function show_cert(string $filename): array
    {
        return $this->exec("show-cert $filename");
    }

    public function import_req(string $request_file_path, string $short_basename): array
    {
        return $this->exec("import-req $request_file_path $short_basename");
    }

    public function export_p7(string $filename): array
    {
        return $this->exec("export-p7 $filename");
    }

    public function export_p12(string $filename): array
    {
        return $this->exec("export-p12 $filename");
    }

    public function set_rsa_pass(string $filename): array
    {
        return $this->exec("set-rsa-pass $filename");
    }

    public function set_ec_pass(string $filename): array
    {
        return $this->exec("set-ec-pass $filename");
    }
}
