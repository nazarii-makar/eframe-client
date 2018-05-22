<?php

namespace EFrame\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Support\Collection;
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
    use Concerns\Authenticatable,
        Concerns\HasHandlers;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $config;

    /**
     * @var Collection
     */
    protected $options;

    /**
     * ProxyClient constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = collect($config);

        $this->refreshOptions();

        $this->boot();
    }

    /**
     * @return Client
     */
    protected function factory()
    {
        $options = collect($this->options->all());

        $this->touch($options);

        return new Client($options->toArray());
    }

    /**
     * @return mixed
     */
    protected function getAuthType()
    {
        return $this->config->get('auth');
    }

    /**
     * @return mixed
     */
    protected function getAuthCredentials()
    {
        return $this->config->get('auth_config');
    }

    /**
     * The "booting" method of the client.
     *
     * @return void
     */
    protected function boot()
    {
        $this->bootOptions();

        $this->bootTraits();
    }

    /**
     * @return void
     */
    protected function bootOptions()
    {
        $this->options->offsetSet('base_uri', $this->getUri());
    }

    /**
     * Boot all of the bootable traits on the cloient.
     *
     * @return void
     */
    protected function bootTraits()
    {
        $class = static::class;

        foreach (class_uses_recursive($class) as $trait) {
            if (method_exists($class, $method = 'boot'.class_basename($trait))) {
                forward_static_call([$class, $method]);
            }
        }
    }

    /**
     * @param Collection $options
     */
    protected function touch(Collection $options)
    {
        $this->touchTraits($options);
    }

    /**
     * @param Collection $options
     */
    protected function touchTraits(Collection $options)
    {
        $class = static::class;

        foreach (class_uses_recursive($class) as $trait) {
            if (method_exists($class, $method = 'touch'.class_basename($trait))) {
                forward_static_call([$class, $method], $options);
            }
        }
    }

    /**
     * @return void
     */
    protected function refreshOptions()
    {
        $this->options = collect($this->config->get('config'));
    }

    /**
     * @return string
     */
    protected function getUri()
    {
        $base_uri = rtrim($this->config->get('base_uri'), '/');
        $path     = ltrim($this->config->get('path'), '/');

        foreach ($this->config->get('path_options') as $key => $value) {
            $path = str_replace("{{$key}}", $value, $path);
        }

        return "{$base_uri}/{$path}/";
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