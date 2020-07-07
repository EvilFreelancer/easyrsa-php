<?php

declare(strict_types=1);

namespace EasyRSA\Interfaces;

interface ConfigInterface
{
    /**
     * Set configuration parameter
     *
     * @param string           $name                Name of parameters
     * @param string|bool|null $value               Vale of parameter
     * @param bool             $resolveAbsolutePath If need convert relative path to absolute
     *
     * @return \EasyRSA\Interfaces\ConfigInterface
     */
    public function set(string $name, $value = null, bool $resolveAbsolutePath = true): ConfigInterface;

    /**
     * Get parameter by name
     *
     * @param string $name
     *
     * @return string|bool|null
     */
    public function get(string $name);

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
