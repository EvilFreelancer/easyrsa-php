<?php

declare(strict_types=1);

namespace EasyRSA\Interfaces;

interface DownloaderInterface
{
    /**
     * Get latest release of EasyRSA.
     *
     * @return string Information about latest tag/release on GitHub
     * @throws \JsonException
     */
    public function getLatestVersion(): string;

    /**
     * Download latest release to specified path.
     *
     * @return string|null
     * @throws \JsonException
     */
    public function downloadLatestVersion(): ?string;

    /**
     * Extract ZIP archive
     *
     * @return array Array will be filled with every line of output from the command
     */
    public function extractArchive(): array;

    /**
     * Get latest release of EasyRSA and extract it to some folder.
     *
     * @throws \JsonException
     */
    public function getEasyRSA(): void;
}
