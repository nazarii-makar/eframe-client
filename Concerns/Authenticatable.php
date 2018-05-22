<?php

namespace EFrame\Client\Concerns;

use Illuminate\Support\Collection;

/**
 * Trait Authenticatable
 * @package EFrame\Client\Concerns
 */
trait Authenticatable
{
    /**
     * @var bool
     */
    protected $auth;

    /**
     * @return void
     */
    protected function bootAuthenticatable()
    {
        $this->withAuth();
    }

    /**
     * @param Collection $options
     */
    protected function touchAuthenticatable(Collection $options)
    {
        if (!$this->auth) {
            return;
        }

        $options->offsetSet('auth', $this->makeAuth());
    }

    /**
     * @return array
     */
    protected function makeAuth()
    {
        return array_merge(
            array_values($this->getAuthCredentials()), [
                $this->getAuthType()
            ]
        );
    }

    /**
     * @return $this
     */
    public function withAuth()
    {
        $this->auth = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function withoutAuth()
    {
        $this->auth = false;

        return $this;
    }

    /**
     * @return array
     */
    abstract protected function getAuthCredentials();

    /**
     * @return string
     */
    abstract protected function getAuthType();
}