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
     * Path to archive with EasyRSA scripts
     */
    public string $archive = '.' . DIRECTORY_SEPARATOR . 'easy-rsa.tar.gz';

    /**
     * Path to folder with EasyRSA scripts
     */
    public string $scripts = '.' . DIRECTORY_SEPARATOR . 'easy-rsa';

    /**
     * Part to certificates store folder
     */
    public string $certs = '.' . DIRECTORY_SEPARATOR . 'easy-rsa-certs';

    /**
     * If need automatically create required folders
     */
    public bool $autocreate = true;

    /**
     * List of allowed variables
     */
    public const ALLOWED = [
        'scripts',
        'archive',
        'certs',
        'autocreate',
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
    public function set(string $name, $value = null, bool $resolveAbsolutePath = true): ConfigInterface
    {
        if (!in_array($name, self::ALLOWED, true)) {
            throw new InvalidArgumentException('Parameter "' . $name . '" is not in allowed list [' . implode(',', self::ALLOWED) . ']');
        }

        if ('autocreate' === $name) {
            $this->autocreate = (bool) $value;
        } else {
            $this->$name = $resolveAbsolutePath ? $this->resolvePath($value) : $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException If wrong key name provided
     */
    public function get(string $name)
    {
        if (!in_array($name, self::ALLOWED, true)) {
            throw new InvalidArgumentException('Parameter "' . $name . '" is not in allowed list [' . implode(',', self::ALLOWED) . ']');
        }

        return $this->$name;
    }

    /**
     * {@inheritdoc}
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

    /**
     * @codeCoverageIgnore
     * @deprecated Use ->get('certs')
     */
    public function getCerts(): string
    {
        return $this->get('certs');
    }

    /**
     * @param string $path
     *
     * @return \EasyRSA\Interfaces\ConfigInterface
     * @codeCoverageIgnore
     * @deprecated Use ->set('certs')
     */
    public function setCerts(string $path): ConfigInterface
    {
        return $this->set('certs', $path, true);
    }

    /**
     * @codeCoverageIgnore
     * @deprecated Use ->get('scripts')
     */
    public function getScripts(): string
    {
        return $this->get('scripts');
    }

    /**
     * @param string $path
     *
     * @return \EasyRSA\Interfaces\ConfigInterface
     * @codeCoverageIgnore
     * @deprecated Use ->set('scripts')
     */
    public function setScripts(string $path): ConfigInterface
    {
        return $this->set('scripts', $path, true);
    }

    /**
     * @codeCoverageIgnore
     * @deprecated Use ->get('archive')
     */
    public function getArchive(): string
    {
        return $this->get('archive');
    }

    /**
     * @param string $path
     *
     * @return \EasyRSA\Interfaces\ConfigInterface
     * @codeCoverageIgnore
     * @deprecated Use ->set('archive')
     */
    public function setArchive(string $path): ConfigInterface
    {
        return $this->set('archive', $path, true);
    }
}
