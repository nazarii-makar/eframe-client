<?php

namespace EFrame\Client\Facades;

use EFrame\Client\ProxyClient;
use Illuminate\Support\Facades\Facade;

/**
 * Class Client
 * @package EFrame\Client\Facades
 *
 * @method static ProxyClient gateway(string $gateway)
 */
class Client extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'client';
    }
}