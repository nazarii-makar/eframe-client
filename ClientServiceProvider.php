<?php

namespace EFrame\Client;

use EFrame\Client\Client;
use EFrame\Client\GatewayFactory;
use Illuminate\Support\ServiceProvider;

/**
 * Class ClientServiceProvider
 * @package EFrame\Client
 */
class ClientServiceProvider extends ServiceProvider
{
    /**
     * Register the application services
     */
    public function register()
    {
        $this->registerClient();
    }

    /**
     * Register Payment service
     */
    protected function registerClient()
    {
        $this->app->singleton('client', function () {
            return new Client(
                new GatewayFactory($this->config('gateways'))
            );
        });
    }

    /**
     * @param null $key
     * @param null $default
     *
     * @return mixed
     */
    protected function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return config('client');
        }

        return config("client.{$key}", $default);
    }

    /**
     * Bootstrap the application services
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/config/client.php');
        $this->mergeConfigFrom($config, 'client');
    }
}