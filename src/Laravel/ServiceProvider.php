<?php

namespace EasyRSA\Laravel;

use EasyRSA\Worker;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->setUpConfig();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $app = $this->app;

        $app->singleton('easy-rsa.factory', static function ($app) {
            return new Factory();
        });

        $app->singleton('easy-rsa', static function ($app) {
            return new Manager($app, $app['easy-rsa.factory']);
        });

        $app->alias('easy-rsa', Manager::class);

        $app->singleton(Worker::class, static function (Container $app) {
            return $app->make('easy-rsa')->connection();
        });
    }

    protected function setUpConfig(): void
    {
        $source = dirname(__DIR__) . '/config/easy-rsa.php';

        if ($this->app instanceof LaravelApplication) {
            $this->publishes([$source => config_path('easy-rsa.php')], 'config');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('easy-rsa');
        }

        $this->mergeConfigFrom($source, 'easy-rsa');
    }
}
