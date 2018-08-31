<?php

namespace EasyRSA;

class Downloader
{
    /**
     * Url via which possible to get the latest release of EasyRSA
     */
    const URL_LATEST_RELEASE = 'https://api.github.com/repos/OpenVPN/easy-rsa/releases/latest';

    /**
     * @var Config
     */
    private $_config;

    /**
     * Downloader constructor, need configuration for normal usage
     *
     * @param   Config $config
     */
    public function __construct(Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Exec some operation by cURL
     *
     * @param   string $url
     * @param   string|null $filename
     * @return  mixed
     */
    private function curlExec(string $url, string $filename = null)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_USERAGENT, 'useragent');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        // If filename is not set
        if (null !== $filename) {
            $fp = fopen($filename, 'wb+');
            curl_setopt($curl, CURLOPT_FILE, $fp);
        }

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * Get latest release of EasyRSA
     *
     * @return  string
     */
    public function releasesLatest(): string
    {
        $json = $this->curlExec(self::URL_LATEST_RELEASE);
        $json = json_decode($json, true);
        return $json['tarball_url'];
    }

    /**
     * Download latest release to specified path
     *
     * @return  string
     */
    public function downloadLatest(): string
    {
        // Get full path to archive file
        $filename = $this->_config->getArchive();

        // Get url with latest release
        $latest = $this->releasesLatest();

        // Download and return status
        return $this->curlExec($latest, $filename);
    }

    /**
     * Extract ZIP archive
     *
     * @return  array Array will be filled with every line of output from the command
     */
    public function extractArchive(): array
    {
        $scripts = $this->_config->getScripts();
        $archive = $this->_config->getArchive();
        $result = [];

        // Extract only if folder exist
        if (@mkdir($scripts, 0755, true) || is_dir($scripts)) {
            error_log("Folder '$scripts' created");
            exec("/usr/bin/env tar xfvz $archive -C $scripts --strip-components=1", $result);
        } else {
            throw new \RuntimeException("Folder $scripts can't be created");
        }
        return $result;
    }

    /**
     * Get latest release of EasyRSA and extract it to some folder
     */
    public function getEasyRSA()
    {
        $this->downloadLatest();
        $this->extractArchive();
    }
}
