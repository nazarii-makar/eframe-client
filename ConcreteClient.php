<?php

namespace EFrame\Client;

use EFrame\Support\Model;
use EFrame\Client\Facades\Client;
use EFrame\Client\Exceptions\Exception;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Container\Container as Application;

/**
 * Class ConcreteClient
 * @package EFrame\Client
 */
abstract class ConcreteClient
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var \EFrame\Client\ProxyClient
     */
    protected $client;

    /**
     * @var \EFrame\Support\Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $handlers = [];

    /**
     * ConcreteClient constructor.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->client = Client::gateway($this->gateway());

        $this->registerHandlers();

        $this->boot();
    }

    /**
     * @return string|null
     */
    protected function model() {
        return null;
    }

    /**
     * @return string
     */
    abstract protected function gateway();

    /**
     * @param array $attributes
     * @param bool  $exists
     *
     * @return \EFrame\Support\Model
     */
    protected function newModelInstance($attributes = [], $exists = true)
    {
        return $this->makeModel()->newInstance($attributes, $exists);
    }

    /**
     * @param array $models
     *
     * @return \Illuminate\Support\Collection
     */
    protected function newCollection(array $models = [])
    {
        return $this->makeModel()->newCollection($models);
    }

    /**
     * @param array $items
     *
     * @return \Illuminate\Support\Collection
     */
    protected function hydrate(array $items = [])
    {
        return $this->makeModel()->newCollection(array_map(function ($item) {
            $this->newModelInstance($item);
        }, $items));
    }

    /**
     * @return void
     */
    protected function boot()
    {
        //
    }

    /**
     * @return \EFrame\Support\Model
     */
    protected function makeModel()
    {
        if (is_null($this->model)) {
            $this->refreshModel();
        }

        return $this->model;
    }

    /**
     * @return \EFrame\Support\Model
     */
    protected function refreshModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new Exception(sprintf(
                'Class %s must be instance of %s',
                $this->model(), Model::class
            ));
        }

        return $this->model = $model;
    }

    /**
     * @return \EFrame\Support\Model
     */
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * @return void
     */
    protected function registerHandlers()
    {
        foreach ($this->handlers as $callback) {
            if (!is_callable($callback)) {
                throw new Exception('Client handlers must be callable');
            }

            list ($class, $method) = $callback;

            $this->client->stack()->push($callback(), "{$class}::{$method}");
        }
    }

    /**
     * @param string $json
     * @param bool   $assoc
     * @param int    $depth
     * @param int    $options
     *
     * @return mixed
     */
    protected function fromJson(string $json, $assoc = false, $depth = 512, $options = 0)
    {
        return json_decode($json, $assoc, $depth, $options);
    }
}