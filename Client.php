<?php

namespace EFrame\Client;

use Illuminate\Support\Collection;
use EFrame\Client\Exceptions\BadMethodCallException;

/**
 * Class Client
 * @package EFrame\Client
 */
class Client
{
    /**
     * @var GatewayFactory
     */
    protected $factory;

    /**
     * Client constructor.
     *
     * @param GatewayFactory $factory
     */
    public function __construct(GatewayFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param $gateway
     *
     * @return ProxyClient
     */
    public function gateway($gateway)
    {
        return $this->factory->create($gateway);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->resolve($name, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    protected function resolve($name, $arguments)
    {
        throw_unless(
            method_exists($this->gateway, $name),
            new BadMethodCallException(sprintf("Gateway '%s' not support '%s'", get_class($this->gateway), $name))
        );

        return call_user_func_array([$this->gateway, $name], $arguments);
    }
}