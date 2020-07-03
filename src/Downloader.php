<?php

declare(strict_types=1);

namespace EasyRSA;

use EasyRSA\Interfaces\ConfigInterface;
use EasyRSA\Interfaces\DownloaderInterface;
use RuntimeException;

class Downloader implements DownloaderInterface
{
    /**
     * Url via which possible to get the latest release of EasyRSA.
     */
    public const URL_LATEST_RELEASE = 'https://api.github.com/repos/OpenVPN/easy-rsa/releases/latest';

    /**
     * @var \EasyRSA\Interfaces\ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Downloader constructor, need configuration for normal usage.
     *
     * @param \EasyRSA\Interfaces\ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Exec some operation by cURL.
     *
     * TODO: Rewrite to Guzzle
     *
     * @param string      $url
     * @param string|null $filename
     *
     * @return string|null
     */
    private function curlExec(string $url, string $filename = null): ?string
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

        return is_bool($result) ? null : $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestVersion(): string
    {
        $json = $this->curlExec(self::URL_LATEST_RELEASE);
        $json = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return $json['tarball_url'];
    }

    /**
     * {@inheritdoc}
     */
    public function downloadLatestVersion(): ?string
    {
        // Get full path to archive file
        $filename = $this->config->getArchive();

        // Get url with latest release
        $latest = $this->getLatestVersion();

        // Download and return status
        return $this->curlExec($latest, $filename);
    }

    /**
     * {@inheritdoc}
     */
    public function extractArchive(): array
    {
        $scripts = $this->config->getScripts();
        $archive = $this->config->getArchive();
        $result  = [];

        // Extract only if folder exist
        if (mkdir($scripts, 0755, true) || is_dir($scripts)) {
            exec("/usr/bin/env tar xfvz $archive -C $scripts --strip-components=1", $result);
        } else {
            throw new RuntimeException("Folder $scripts can't be created");
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getEasyRSA(): void
    {
        $this->downloadLatestVersion();
        $this->extractArchive();
    }
}
