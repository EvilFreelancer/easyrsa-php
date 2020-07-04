<?php

declare(strict_types=1);

namespace EasyRSA\Interfaces;

interface ConfigInterface
{
    /**
     * Set configuration parameter
     *
     * @param string      $name                Name of parameters
     * @param string|null $value               Vale of parameter
     * @param bool        $resolveAbsolutePath If need convert relative path to absolute
     *
     * @return \EasyRSA\Interfaces\ConfigInterface
     */
    public function set(string $name, string $value = null, bool $resolveAbsolutePath = true): ConfigInterface;

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

    /**
     * Remove '.' and '..' path parts and make path absolute without
     * resolving symlinks.
     *
     * Examples:
     *
     *   resolvePath("test/./me/../now/", false);
     *   => test/now
     *
     *   resolvePath("test///.///me///../now/", true);
     *   => /home/example/test/now
     *
     *   resolvePath("test/./me/../now/", "/www/example.com");
     *   => /www/example.com/test/now
     *
     *   resolvePath("/test/./me/../now/", "/www/example.com");
     *   => /test/now
     *
     * @param string      $path     Absolute or relative path on filesystem
     * @param string|null $basePath Resolve paths relatively to this path. Params:
     *                              STRING: prefix with this path;
     *                              TRUE: use current dir;
     *                              FALSE: keep relative (default)
     *
     * @return string resolved path
     */
    public function resolvePath(string $path, string $basePath = null): string;
}
