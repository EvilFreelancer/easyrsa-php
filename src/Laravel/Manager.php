<?php

namespace EasyRSA\Laravel;

use EasyRSA\Interfaces\WorkerInterface;
use EasyRSA\Worker;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class Manager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected Container $app;

    /**
     * The EasyRSA connection factory instance.
     *
     * @var \EasyRSA\Laravel\Factory
     */
    protected Factory $factory;

    /**
     * The active worker instances.
     *
     * @var array
     */
    protected array $workers = [];

    /**
     * @param \Illuminate\Contracts\Container\Container $app
     * @param \EasyRSA\Laravel\Factory                  $factory
     */
    public function __construct(Container $app, Factory $factory)
    {
        $this->app     = $app;
        $this->factory = $factory;
    }

    /**
     * Get the default worker.
     *
     * @return string
     */
    public function getDefaultWorker(): string
    {
        return $this->app['config']['easy-rsa.defaultWorker'];
    }

    /**
     * Set the default worker.
     *
     * @param string $connection
     */
    public function setDefaultWorker(string $connection): void
    {
        $this->app['config']['easy-rsa.defaultConnection'] = $connection;
    }

    /**
     * Get the configuration for a named worker.
     *
     * @param string $name
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getConfig(string $name): array
    {
        $connections = $this->app['config']['easy-rsa.connections'];

        if (null === $config = Arr::get($connections, $name)) {
            throw new InvalidArgumentException("EasyRSA worker [$name] not configured.");
        }

        return $config;
    }

    /**
     * Make a new worker.
     *
     * @param string $name
     *
     * @return \EasyRSA\Worker
     */
    protected function makeWorker(string $name): Worker
    {
        $config = $this->getConfig($name);

        return $this->factory->make($config);
    }

    /**
     * Return all of the created workers.
     *
     * @return array
     */
    public function getWorkers(): array
    {
        return $this->workers;
    }

    /**
     * Instantiate worker object
     *
     * @param string|null $name
     *
     * @return \EasyRSA\Interfaces\WorkerInterface
     */
    public function worker(string $name = null): WorkerInterface
    {
        $name = $name ?: $this->getDefaultWorker();

        if (!isset($this->workers[$name])) {
            $worker = $this->makeWorker($name);

            $this->workers[$name] = $worker;
        }

        return $this->workers[$name];
    }

    /**
     * Dynamically pass methods to the default worker.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return call_user_func_array([$this->worker(), $method], $parameters);
    }
}
