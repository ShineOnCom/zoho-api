<?php

namespace ShineOnCom\Zoho\Helpers\Testing;

use ShineOnCom\Zoho\Zoho;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use ReflectionMethod;

/**
 * Class ZohoMock
 */
class ZohoMock extends Zoho
{
    /** @var array $requestHistory */
    protected $requestHistory = [];

    /**
     * ZohoMock constructor.
     * @param array $responseStack
     */
    public function __construct($responseStack)
    {
        // Create a mock and queue two responses.
        $mock = new MockHandler($responseStack);
        $handler = HandlerStack::create($mock);

        // Add the history middleware to the handler stack.
        $history = Middleware::history($this->requestHistory);
        $handler->push($history);

        $token = 'test_token';

        // Calling Guzzle constructor
        try {
            $reflectionMethod = new ReflectionMethod(get_parent_class(get_parent_class($this)), '__construct');

            $reflectionMethod->invoke($this, [
                'base_uri' => 'http://test.com',
                'headers' => [
                    'Authorization' => "Zoho-oauthtoken {$token}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json; charset=utf-8;',
                ],
                'handler' => $handler
            ]);

        } catch (\ReflectionException $e) {}
    }

    /**
     * http://docs.guzzlephp.org/en/stable/testing.html
     * @return array
     */
    public function getLastTransaction()
    {
        return $this->requestHistory[count($this->requestHistory) - 1];
    }

    /**
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getLastRequest()
    {
        return $this->getLastTransaction()['request'];
    }

    /**
     * @return string
     */
    public function lastRequestMethod()
    {
        return $this->getLastRequest()->getMethod();
    }

    /**
     * @return string
     */
    public function lastRequestUri()
    {
        return $this->getLastRequest()->getUri()->getPath();
    }

    /**
     * @return \GuzzleHttp\Psr7\Response
     */
    public function getLastResponse()
    {
        return $this->getLastTransaction()['response'];
    }

    /**
     * @return integer
     */
    public function lastResponseStatusCode()
    {
        return $this->getLastResponse()->getStatusCode();
    }
}