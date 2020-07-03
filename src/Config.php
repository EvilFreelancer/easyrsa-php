<?php

declare(strict_types=1);

namespace EasyRSA;

use function count;
use EasyRSA\Interfaces\ConfigInterface;

/**
 * Class Config.
 */
class Config implements ConfigInterface
{
    /**
     * Name of file (with or without full path to this file).
     */
    public string $archive = '.' . DIRECTORY_SEPARATOR . 'easy-rsa.tar.gz';

    /**
     * Path to folder with easy-rsa scripts.
     */
    public string $scripts = '.' . DIRECTORY_SEPARATOR . 'easy-rsa';

    /**
     * Path to folder with certificates.
     */
    public string $certs = '.';

    /**
     * Config constructor.
     *
     * @param array<string, string> $parameters
     */
    public function __construct(array $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, string $value): ConfigInterface
    {
        $this->$name = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): string
    {
        return $this->$name;
    }

    /**
     * {@inheritdoc}
     */
    public function getCerts(): string
    {
        return $this->certs;
    }

    /**
     * {@inheritdoc}
     */
    public function setCerts(string $path): ConfigInterface
    {
        return $this->set('certs', $this->resolvePath($path));
    }

    /**
     * {@inheritdoc}
     */
    public function getScripts(): string
    {
        return $this->scripts;
    }

    /**
     * {@inheritdoc}
     */
    public function setScripts(string $path): ConfigInterface
    {
        return $this->set('scripts', $this->resolvePath($path));
    }

    /**
     * {@inheritdoc}
     */
    public function getArchive(): string
    {
        return $this->archive;
    }

    /**
     * {@inheritdoc}
     */
    public function setArchive(string $path): ConfigInterface
    {
        return $this->set('archive', $this->resolvePath($path));
    }

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
     * @param string $path     Absolute or relative path on filesystem
     * @param mixed  $basePath Resolve paths relatively to this path. Params:
     *                         STRING: prefix with this path;
     *                         TRUE: use current dir;
     *                         FALSE: keep relative (default)
     *
     * @return string resolved path
     */
    protected function resolvePath(string $path, $basePath = true): string
    {
        // Make absolute path
        if (DIRECTORY_SEPARATOR !== $path[0]) {
            if (true === $basePath) {
                // Get PWD first to avoid getcwd() resolving symlinks if in symlinked folder
                $path = (getenv('PWD') ?: getcwd()) . DIRECTORY_SEPARATOR . $path;
            } elseif ('' !== $basePath) {
                $path = $basePath . DIRECTORY_SEPARATOR . $path;
            }
        }

        // Resolve '.' and '..'
        $components = [];
        foreach (explode(DIRECTORY_SEPARATOR, rtrim($path, DIRECTORY_SEPARATOR)) as $name) {
            if ('..' === $name) {
                array_pop($components);
            } elseif ('.' !== $name && !(count($components) && '' === $name)) {
                // … && !(count($components) && $name === '') - we want to keep initial '/' for abs paths
                $components[] = $name;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $components);
    }
}
