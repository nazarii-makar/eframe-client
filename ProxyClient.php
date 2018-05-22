<?php

namespace EFrame\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ProxyClient
 * @package EFrame\Client
 *
 * @method ResponseInterface get(string | UriInterface $uri, array $options = [])
 * @method ResponseInterface head(string | UriInterface $uri, array $options = [])
 * @method ResponseInterface put(string | UriInterface $uri, array $options = [])
 * @method ResponseInterface post(string | UriInterface $uri, array $options = [])
 * @method ResponseInterface patch(string | UriInterface $uri, array $options = [])
 * @method ResponseInterface delete(string | UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface getAsync(string | UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface headAsync(string | UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface putAsync(string | UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface postAsync(string | UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface patchAsync(string | UriInterface $uri, array $options = [])
 * @method Promise\PromiseInterface deleteAsync(string | UriInterface $uri, array $options = [])
 * @method ResponseInterface send(RequestInterface $request, array $options = [])
 * @method ResponseInterface request($method, $uri = '', array $options = [])
 * @method Promise\PromiseInterface sendAsync(RequestInterface $request, array $options = [])
 * @method Promise\PromiseInterface requestAsync($method, $uri = '', array $options = [])
 */
class ProxyClient
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $config;

    /**
     * @var bool
     */
    protected $auth = true;

    /**
     * ProxyClient constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = collect($config);
    }

    /**
     * @return Client
     */
    protected function factory()
    {
        $config             = $this->config->get('config');
        $config['base_uri'] = $this->buildUri();
        $config['auth']     = $this->makeAuth();

        return new Client($config);
    }

    /**
     * @return array
     */
    protected function makeAuth()
    {
        if (!$this->auth || is_null($auth_config = $this->config->get('auth_config'))) {
            return null;
        }

        return array_merge(
            array_values($auth_config),
            [$this->config->get('auth')]
        );
    }

    /**
     * @return string
     */
    protected function buildUri()
    {
        $path = $this->config->get('path', '');

        $path = rtrim($path, '/') . '/';

        foreach ($this->config->get('path_options') as $key => $value) {
            $mask = "{{$key}}";

            $path = str_replace($mask, $value, $path);
        }

        return sprintf(
            '%s/%s',
            rtrim($this->config->get('base_uri'), '/'),
            ltrim($path, '/')
        );
    }

    /**
     * @return ProxyClient
     */
    public function withAuth()
    {
        $this->auth = true;

        return $this;
    }

    /**
     * @return ProxyClient
     */
    public function withoutAuth()
    {
        $this->auth = false;

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->factory(), $name], $arguments);
    }
}