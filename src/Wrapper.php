<?php

declare(strict_types=1);

namespace EasyRSA;

use EasyRSA\Interfaces\ConfigInterface;
use RuntimeException;

class Wrapper
{
    /**
     * Main location of EasyRSA scripts
     *
     * @var string
     */
    private string $scripts;

    /**
     * Path to folder with certificates
     *
     * @var string
     */
    private string $certs;

    /**
     * If need to make certificate without password
     */
    private const NOPASS = 'nopass';

    /**
     * If need to enable debug mode
     */
    public bool $dryRun = false;

    /**
     * Wrapper constructor, need configuration for normal usage
     *
     * @param \EasyRSA\Interfaces\ConfigInterface $config
     *
     * @throws \RuntimeException
     */
    public function __construct(ConfigInterface $config)
    {
        // Create folders for certificates
        $this->certs = $config->getCerts();
        putenv("EASYRSA_PKI={$this->certs}");
        if (!mkdir($this->certs, 0755, true) || is_dir($this->certs)) {
            throw new RuntimeException("Folder {$this->certs} can't be created");
        }

        $this->scripts = $config->getScripts();
    }

    /**
     * Show content of certificate file
     *
     * @param string $filename Only name of file must be set, without path
     *
     * @return string|null
     */
    public function getContent(string $filename): ?string
    {
        switch ($filename) {
            case 'ca.crt':
            case 'dh.pem':
                $path = $this->certs . '/' . $filename;
                break;
            default:
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                switch ($ext) {
                    case 'crt':
                        $path = $this->certs . '/issued/' . $filename;
                        break;
                    case 'key':
                        $path = $this->certs . '/private/' . $filename;
                        break;
                    case 'req':
                        $path = $this->certs . '/reqs/' . $filename;
                        break;
                    default:
                        return null;
                        break;
                }
                break;
        }

        return file_get_contents($path);
    }

    /**
     * Execute some command and return result
     *
     * @param string $cmd
     *
     * @return array<string>
     */
    private function exec(string $cmd): array
    {
        $command = $this->scripts . '/easyrsa3/easyrsa --batch ' . $cmd;

        // In dry run mode need just return command without real execution
        if ($this->dryRun) {
            $result = [$command];
        } else {
            chdir($this->certs);
            exec($this->scripts . '/easyrsa3/easyrsa --batch ' . $cmd, $result);
        }

        return $result;
    }

    /**
     * @return array<string>
     */
    public function init_pki(): array
    {
        return $this->exec('init-pki');
    }

    /**
     * @param bool $nopass
     *
     * @return array<string>
     */
    public function build_ca(bool $nopass = false): array
    {
        $param = $nopass ? self::NOPASS : '';

        return $this->exec("build-ca $param");
    }

    /**
     * @return array<string>
     */
    public function gen_dh(): array
    {
        return $this->exec('gen-dh');
    }

    /**
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function gen_req(string $name, bool $nopass = false): array
    {
        $param = $nopass ? self::NOPASS : '';

        return $this->exec("gen-req $name $param");
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function sign_req_client(string $filename): array
    {
        return $this->exec("sign-req server $filename");
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function sign_req_server(string $filename): array
    {
        return $this->exec("sign-req client $filename");
    }

    /**
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function build_client_full(string $name, bool $nopass = false): array
    {
        $param = $nopass ? self::NOPASS : '';

        return $this->exec("build-client-full $name $param");
    }

    /**
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function build_server_full(string $name, bool $nopass = false): array
    {
        $param = $nopass ? self::NOPASS : '';

        return $this->exec("build-server-full $name $param");
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function revoke(string $filename): array
    {
        return $this->exec("revoke $filename");
    }

    /**
     * @return array<string>
     */
    public function gen_crl(): array
    {
        return $this->exec('gen-crl');
    }

    /**
     * @return array<string>
     */
    public function update_db(): array
    {
        return $this->exec('update-db');
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function show_req(string $filename): array
    {
        return $this->exec("show-req $filename");
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function show_cert(string $filename): array
    {
        return $this->exec("show-cert $filename");
    }

    /**
     * @param string $request_file_path
     * @param string $short_basename
     *
     * @return array<string>
     */
    public function import_req(string $request_file_path, string $short_basename): array
    {
        return $this->exec("import-req $request_file_path $short_basename");
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function export_p7(string $filename): array
    {
        return $this->exec("export-p7 $filename");
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function export_p12(string $filename): array
    {
        return $this->exec("export-p12 $filename");
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function set_rsa_pass(string $filename): array
    {
        return $this->exec("set-rsa-pass $filename");
    }

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function set_ec_pass(string $filename): array
    {
        return $this->exec("set-ec-pass $filename");
    }
}
