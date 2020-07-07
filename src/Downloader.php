<?php

declare(strict_types=1);

namespace EasyRSA;

use EasyRSA\Interfaces\DownloaderInterface;
use splitbrain\PHPArchive\Tar;

class Downloader extends Worker implements DownloaderInterface
{
    /**
     * Url via which possible to get the latest release of EasyRSA.
     */
    public const URL_LATEST_RELEASE = 'https://api.github.com/repos/OpenVPN/easy-rsa/releases/latest';

    /**
     * Exec some operation by cURL.
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
     *
     * @throws \splitbrain\PHPArchive\ArchiveIllegalCompressionException If archive is broken
     * @throws \splitbrain\PHPArchive\ArchiveIOException If not possible to read archive
     * @throws \splitbrain\PHPArchive\ArchiveCorruptedException If archive corrupted
     */
    public function extractArchive(): array
    {
        $tar = new Tar();
        $tar->open($this->config->get('archive'));
        $tar->extract($this->config->get('scripts'), 1);

        return $tar->contents();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \splitbrain\PHPArchive\ArchiveIllegalCompressionException If archive is broken
     * @throws \splitbrain\PHPArchive\ArchiveIOException If not possible to read archive
     * @throws \splitbrain\PHPArchive\ArchiveCorruptedException If archive corrupted
     */
    public function getEasyRSA(): void
    {
        $this->downloadLatestVersion();
        $this->extractArchive();
    }
}
