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
    protected ConfigInterface $config;

    /**
     * If need to enable debug mode
     */
    public bool $dryRun = false;

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
        putenv("EASYRSA_PKI={$this->config->get('certs')}");
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
        $command = $this->config->get('scripts') . '/easyrsa3/easyrsa --batch ' . $cmd;

        // In dry run mode need just return command without real execution
        if ($this->dryRun) {
            $result = [$command];
        } else {
            // Create folder if not exist
            if (!mkdir($this->config->get('certs'), 0755, true) && !is_dir($this->config->get('certs'))) {
                throw new RuntimeException("Folder \"{$this->config->get('certs')}\" can't be created");
            }

            chdir($this->config->get('certs'));
            exec($command, $result);
        }

        return $result;
    }
}
