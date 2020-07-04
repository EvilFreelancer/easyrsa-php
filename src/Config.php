<?php

declare(strict_types=1);

namespace EasyRSA;

use function count;
use EasyRSA\Interfaces\ConfigInterface;
use InvalidArgumentException;

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
    public string $certs = '.' . DIRECTORY_SEPARATOR . 'easy-rsa-certs';

    /**
     * List of allowed variables
     */
    public const ALLOWED = [
        'scripts',
        'archive',
        'certs',
    ];

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
     *
     * @throws \InvalidArgumentException If wrong key name provided
     */
    public function set(string $name, string $value = null, bool $resolveAbsolutePath = true): ConfigInterface
    {
        if (!in_array($name, self::ALLOWED, true)) {
            throw new InvalidArgumentException('Parameter "' . $name . '" is not in allowed list [' . implode(',', self::ALLOWED) . ']');
        }

        $this->$name = $resolveAbsolutePath ? $this->resolvePath($value) : $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException If wrong key name provided
     */
    public function get(string $name): string
    {
        if (!in_array($name, self::ALLOWED, true)) {
            throw new InvalidArgumentException('Parameter "' . $name . '" is not in allowed list [' . implode(',', self::ALLOWED) . ']');
        }

        return $this->$name;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getCerts(): string
    {
        return $this->get('certs');
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function setCerts(string $path): ConfigInterface
    {
        return $this->set('certs', $path, true);
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getScripts(): string
    {
        return $this->get('scripts');
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function setScripts(string $path): ConfigInterface
    {
        return $this->set('scripts', $path, true);
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getArchive(): string
    {
        return $this->get('archive');
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function setArchive(string $path): ConfigInterface
    {
        return $this->set('archive', $path, true);
    }

    /**
     * {@inheritDoc}
     */
    public function resolvePath(string $path, string $basePath = null): string
    {
        // Make absolute path
        if (DIRECTORY_SEPARATOR !== $path[0]) {
            if (null === $basePath) {
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
            } elseif ('.' !== $name && !(count($components) > 0 && '' === $name)) {
                // … && !(count($components) && $name === '') - we want to keep initial '/' for abs paths
                $components[] = $name;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $components);
    }
}
