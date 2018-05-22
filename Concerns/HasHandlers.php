<?php

namespace EFrame\Client\Concerns;

use GuzzleHttp\HandlerStack;
use Illuminate\Support\Collection;

/**
 * Trait HasHandlers
 * @package EFrame\Client\Concerns
 */
trait HasHandlers
{
    /**
     * @var HandlerStack
     */
    protected $stack;

    /**
     * @return void
     */
    protected function bootHasHandlers()
    {
        $this->refreshStack();
    }

    /**
     * @param Collection $options
     */
    protected function touchHasHandlers(Collection $options)
    {
        $options->offsetSet('handler', $this->stack);
    }

    /**
     * @return HandlerStack
     */
    public function stack()
    {
        return $this->stack;
    }

    /**
     * @return ProxyClient
     */
    public function refreshStack()
    {
        $this->stack = HandlerStack::create();

        return $this;
    }
}