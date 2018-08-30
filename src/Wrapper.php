<?php

namespace EasyRSA;

class Wrapper
{
    /**
     * Main location of easyrsa scripts
     * @var string
     */
    private $_scripts_folder;

    /**
     * Path to folder with certificates
     * @var string
     */
    private $_certs_folder;

    /**
     * The rrequest (.req) file
     * @var string
     */
    private $_import_req;

    /**
     * Wrapper constructor, need configuration for normal usage
     *
     * @param   Config $config
     */
    public function __construct(Config $config)
    {
        $this->_certs_folder = realpath($config->getCertsFolder());
        $this->_scripts_folder = realpath($config->getFolder()) . '/easyrsa3';
        $this->_import_req = $this->_certs_folder . '/import.req';
    }

    /**
     * Execute some command and return result
     *
     * @param   string $cmd
     * @return  array
     */
    private function exec(string $cmd): array
    {
        chdir($this->_certs_folder);
        exec($this->_scripts_folder . '/easyrsa --batch ' . $cmd, $result);
        return $result;
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

    public function init_pki(): array
    {
        return $this->exec('init-pki');
    }

    public function build_ca(bool $nopass = false): array
    {
        $param = $nopass ? 'nopass' : '';
        return $this->exec("build-ca $param");
    }

    public function gen_req(string $name, bool $nopass = false): array
    {
        $param = $nopass ? 'nopass' : '';
        return $this->exec("gen-req $name $param");
    }

    public function import_req(string $filename): array
    {
        return $this->exec("import-req {$this->_import_req} $filename");
    }

    public function show_req(string $filename): array
    {
        return $this->exec("show-req $filename");
    }

    public function sign_req_client(string $filename): array
    {
        return $this->exec("sign-req server $filename");
    }

    public function sign_req_server(string $filename): array
    {
        return $this->exec("sign-req client $filename");
    }

    public function gen_dh(): array
    {
        return $this->exec('gen-dh');
    }

}
