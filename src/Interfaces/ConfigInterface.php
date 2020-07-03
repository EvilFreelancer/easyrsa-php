<?php

declare(strict_types=1);

namespace EasyRSA\Interfaces;

interface ConfigInterface
{
    /**
     * Set configuration parameter
     *
     * @param string $name  Name of parameters
     * @param string $value Vale of parameter
     *
     * @return \EasyRSA\Interfaces\ConfigInterface
     */
    public function set(string $name, string $value): ConfigInterface;

    /**
     * Get parameter by name
     *
     * @param string $name
     *
     * @return string
     */
    public function get(string $name): string;

    /**
     * Get path with certificates.
     *
     * @return string
     */
    public function getCerts(): string;

    /**
     * Set full path to folder with certificates.
     *
     * @param string $path Path to folder which contains certificates
     *
     * @return \EasyRSA\Interfaces\ConfigInterface
     */
    public function setCerts(string $path): ConfigInterface;

    /**
     * Get full path to folder with scripts.
     *
     * @return string
     */
    public function getScripts(): string;

    /**
     * Set path with easy-rsa scripts.
     *
     * @param string $path Path to folder with EasyRSA scripts
     *
     * @return \EasyRSA\Interfaces\ConfigInterface
     */
    public function setScripts(string $path): ConfigInterface;

    /**
     * Get archive file with full path.
     *
     * @return string
     */
    public function getArchive(): string;

    /**
     * Set easy-rsa archive file with full path.
     *
     * @param string $path Path to file with archive
     *
     * @return \EasyRSA\Interfaces\ConfigInterface
     */
    public function setArchive(string $path): ConfigInterface;
}
