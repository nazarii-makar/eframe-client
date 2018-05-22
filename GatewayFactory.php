<?php

namespace EFrame\Client;

use Illuminate\Support\Collection;
use EFrame\Client\Exceptions\RuntimeException;
use EFrame\Client\Exceptions\InvalidArgumentException;

/**
 * Class GatewayFactory
 * @package EFrame\Client
 */
class GatewayFactory
{
    /**
     * @var Collection
     */
    protected $gateways;

    /**
     * GatewayFactory constructor.
     *
     * @param array $gateways
     */
    public function __construct($gateways = [])
    {
        $this->replace($gateways);
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->gateways;
    }

    /**
     * @param array $gateways
     *
     * @return $this
     */
    public function replace($gateways = [])
    {
        $this->gateways = collect($gateways);

        return $this;
    }

    /**
     * @param $gateway
     *
     * @return $this
     */
    public function register($gateway, $options = [])
    {
        if (!$this->gateways->has($gateway)) {
            $this->gateways->offsetSet($gateway, collect($options));
        }

        return $this;
    }

    /**
     * @param string $gateway
     *
     * @return ProxyClient
     */
    public function create($gateway)
    {
        throw_unless(
            $this->gateways->has($gateway),
            new RuntimeException("Gateway '{$gateway}' not found.")
        );

        return new ProxyClient($this->gateways->get($gateway));
    }
}