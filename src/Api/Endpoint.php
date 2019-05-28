<?php

namespace ShineOnCom\Zoho\Api;

use ShineOnCom\Zoho\Zoho;

/**
 * Class Endpoint
 *
 * @mixin Zoho
 * @property string endpoint
 * @property array ids
 */
abstract class Endpoint
{
    /** @var Zoho $client */
    protected $client;

    /**
     * Endpoint constructor.
     *
     * @param Zoho $client
     */
    public function __construct(Zoho $client)
    {
        $this->client = $client;
    }

    /**
     * Set our endpoint by accessing it via a property.
     *
     * @param  string $property
     * @return $this
     */
    public function __get($property)
    {
        if (isset($this->$property)) {
            return $this->$property;
        }

        return $this->client->__get($property);
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->client->$method(...$parameters);
    }
}
