<?php

declare(strict_types=1);

namespace EasyRSA;

use EasyRSA\Interfaces\ConfigInterface;
use EasyRSA\Interfaces\WorkerInterface;
use RuntimeException;

class Worker implements WorkerInterface
{
    /**
     * @var \EasyRSA\Interfaces\ConfigInterface
     */
    protected $config;

    /**
     * If need to enable debug mode
     */
    public $dryRun = false;

    /**
     * Prefix of environment variables understood by the easyrsa shell script
     */
    protected const ENV_PREFIX = 'EASYRSA_';

    /**
     * Wrapper constructor, need configuration for normal usage
     *
     * @param \EasyRSA\Interfaces\ConfigInterface|array $config
     *
     * @throws \RuntimeException
     */
    public function __construct($config = [])
    {
        if (is_array($config)) {
            $config = new Config($config);
        }

        if ($config instanceof ConfigInterface) {
            $this->config = $config;
        }

        // Must be set to enable ability execute shell scripts in background
        $this->exportPki();
    }

    /**
     * Export the PKI directory (from worker config) into the process
     * environment and keep $_ENV in sync so getEnv() reports it too.
     */
    protected function exportPki(): void
    {
        $pki = $this->config->get('certs');

        putenv("EASYRSA_PKI={$pki}");
        $_ENV['EASYRSA_PKI'] = (string) $pki;
    }

    /**
     * Load variables from "vars" style file (KEY=VALUE lines) into the process
     * environment, so that the spawned easyrsa script can see them.
     *
     * Only variables with the EASYRSA_ prefix are exported, comments and empty
     * lines are ignored. Values may be wrapped in single or double quotes.
     *
     * Note: Dotenv loaders (e.g. vlucas/phpdotenv) do not export loaded values
     * to the environment of child processes by default, so plain Dotenv::load()
     * is not enough. Either use this method or pass variables via setEnv().
     *
     * @param string $path Path to vars file (vars or vars.example format)
     *
     * @return array<string, string> Loaded variables
     *
     * @throws \RuntimeException If vars file is not readable
     */
    public function loadVars(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new RuntimeException("Vars file \"{$path}\" can't be read");
        }

        $vars = [];

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            // Skip comments and lines without assignment
            if ('' === $line || 0 === strpos($line, '#') || false === strpos($line, '=')) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);

            $name = trim($name);
            $value = trim($value);

            // Strip surrounding quotes ("value" or 'value')
            if (strlen($value) > 1 && ('"' === $value[0] || "'" === $value[0]) && $value[0] === substr($value, -1)) {
                $value = substr($value, 1, -1);
            }

            if (0 === strpos($name, self::ENV_PREFIX)) {
                $vars[$name] = $value;
            }
        }

        return $this->setEnv($vars);
    }

    /**
     * Put variables into the process environment. Only variables with the
     * EASYRSA_ prefix are exported, others are silently ignored.
     *
     * @param array<string, string> $vars
     *
     * @return array<string, string> Exported variables
     */
    public function setEnv(array $vars): array
    {
        $exported = [];

        foreach ($vars as $name => $value) {
            if (!is_string($name) || 0 !== strpos($name, self::ENV_PREFIX)) {
                continue;
            }

            putenv("{$name}={$value}");
            $_ENV[$name] = (string) $value;
            $exported[$name] = (string) $value;
        }

        return $exported;
    }

    /**
     * Get current values of EASYRSA_ prefixed variables visible to subprocess
     *
     * @return array<string, string>
     */
    public function getEnv(): array
    {
        $vars = [];

        foreach ($_ENV as $name => $value) {
            if (is_string($name) && 0 === strpos($name, self::ENV_PREFIX)) {
                $vars[$name] = (string) $value;
            }
        }

        ksort($vars);

        return $vars;
    }

    /**
     * Sync EASYRSA_ prefixed variables from $_ENV/$_SERVER into the process
     * environment. Values loaded by libraries such as Dotenv (immutable mode)
     * live only in these superglobals and are invisible to child processes
     * until exported here. Existing real environment values are overwritten,
     * EASYRSA_PKI is always owned by the worker config.
     */
    protected function syncEnv(): void
    {
        foreach ([$_ENV, $_SERVER] as $source) {
            foreach ($source as $name => $value) {
                if (!is_string($name) || !is_scalar($value)) {
                    continue;
                }

                if (0 === strpos($name, self::ENV_PREFIX) && 'EASYRSA_PKI' !== $name) {
                    putenv("{$name}={$value}");
                }
            }
        }

        // PKI location always follows the worker configuration
        $this->exportPki();
    }

    /**
     * @return \EasyRSA\Interfaces\ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(string $filename): ?string
    {
        switch ($filename) {
            case 'ca.crt':
            case 'dh.pem':
                $path = $this->config->get('certs') . '/' . $filename;
                break;
            default:
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                switch ($ext) {
                    case 'crt':
                        $path = $this->config->get('certs') . '/issued/' . $filename;
                        break;
                    case 'key':
                        $path = $this->config->get('certs') . '/private/' . $filename;
                        break;
                    case 'req':
                        $path = $this->config->get('certs') . '/reqs/' . $filename;
                        break;
                    default:
                        return null;
                        break;
                }
                break;
        }

        return file_get_contents($path);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException Of folder with certs unable to create or if is not a folder
     */
    public function exec(string $cmd): array
    {
        // Make EASYRSA_ variables (e.g. loaded from a vars file or Dotenv)
        // visible to the spawned easyrsa subprocess before launching it
        $this->syncEnv();

        $command = $this->config->get('scripts') . '/easyrsa3/easyrsa --batch ' . $cmd;

        // In dry run mode need just return command without real execution
        if ($this->dryRun) {
            $result = [$command];
        } else {
            // Create folder if not exist
            if (
                !file_exists($this->config->get('certs'))
                && !mkdir($this->config->get('certs'), 0755, true)
                && !is_dir($this->config->get('certs'))
            ) {
                throw new RuntimeException("Folder \"{$this->config->get('certs')}\" can't be created");
            }

            chdir($this->config->get('certs'));
            exec($command, $result);
        }

        return $result;
    }
}
