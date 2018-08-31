<?php

namespace EasyRSA;

class Config
{
    /**
     * Name of file (with or without full path to this file)
     * @var string
     */
    private $_archive = './easy-rsa.tar.gz';

    /**
     * Path to folder with easy-rsa scripts
     * @var string
     */
    private $_scripts = './easy-rsa';

    /**
     * Path to folder with certificates
     * @var string
     */
    private $_certs = '.';

    /**
     * Get path with certificates
     *
     * @return string
     */
    public function getCerts(): string
    {
        return $this->_certs;
    }

    /**
     * Set full path to folder with certificates
     *
     * @param   string $folder
     * @return  Config
     */
    public function setCerts(string $folder): self
    {
        $this->_certs = $this->resolvePath($folder);
        return $this;
    }

    /**
     * Get full path to folder with scripts
     *
     * @return  string
     */
    public function getScripts(): string
    {
        return $this->_scripts;
    }

    /**
     * Set path with easy-rsa scripts
     *
     * @param   string $folder
     * @return  Config
     */
    public function setScripts(string $folder): self
    {
        $this->_scripts = $this->resolvePath($folder);
        return $this;
    }

    /**
     * Get archive file with full path
     *
     * @return  string
     */
    public function getArchive(): string
    {
        return $this->_archive;
    }

    /**
     * Set easy-rsa archive file with full path
     *
     * @param   string $archive
     * @return  Config
     */
    public function setArchive(string $archive): self
    {
        $this->_archive = $this->resolvePath($archive);
        return $this;
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
     * @param string $path
     * @param mixed $basePath resolve paths realtively to this path. Params:
     *                        STRING: prefix with this path;
     *                        TRUE: use current dir;
     *                        FALSE: keep relative (default)
     * @return string resolved path
     */
    public function resolvePath(string $path, $basePath = true): string
    {
        // Make absolute path
        if ($path[0] !== DIRECTORY_SEPARATOR) {
            if ($basePath === true) {
                // Get PWD first to avoid getcwd() resolving symlinks if in symlinked folder
                $path = (getenv('PWD') ?: getcwd()) . DIRECTORY_SEPARATOR . $path;
            } elseif ('' !== $basePath) {
                $path = $basePath . DIRECTORY_SEPARATOR . $path;
            }
        }

        // Resolve '.' and '..'
        $components = array();
        foreach (explode(DIRECTORY_SEPARATOR, rtrim($path, DIRECTORY_SEPARATOR)) as $name) {
            if ($name === '..') {
                array_pop($components);
            } elseif ($name !== '.' && !(\count($components) && $name === '')) {
                // … && !(count($components) && $name === '') - we want to keep initial '/' for abs paths
                $components[] = $name;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $components);
    }
}
