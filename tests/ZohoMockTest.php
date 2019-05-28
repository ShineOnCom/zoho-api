<?php

namespace ShineOnCom\Zoho\Test;

use ShineOnCom\Zoho\Helpers\Testing\ZohoMock;
use PHPUnit\Framework\TestCase;
use ShineOnCom\Zoho\Zoho;

class ZohoMockTest extends TestCase
{
    /** @test */
    public function it_gets_a_zoho_mock_class()
    {
        $mock = Zoho::fake();

        $this->assertEquals(ZohoMock::class, get_class($mock));
    }

    /** @test */
    public function it_sets_the_correct_headers()
    {
        $api = Zoho::fake();

        $headers = $api->getConfig('headers');

        $this->assertTrue(startsWith($headers['Authorization'], 'Zoho-oauthtoken '));
        $this->assertEquals('application/json', $headers['Accept']);
    }
}