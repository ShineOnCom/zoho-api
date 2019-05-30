<?php

namespace ShineOnCom\Zoho\Test;

use PHPUnit\Framework\TestCase;
use ShineOnCom\Zoho\Helpers\Testing\ModelFactory\ContactFactory;
use ShineOnCom\Zoho\Helpers\Testing\TransactionMock;
use ShineOnCom\Zoho\Models\Contact;
use ShineOnCom\Zoho\Zoho;

class ContactsApiTest extends TestCase
{
    /**
     * GET /Contacts
     * Retrieves a list of contacts.
     *
     * @test
     * @throws \ShineOnCom\Zoho\Exceptions\InvalidOrMissingEndpointException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function it_gets_a_list_of_contacts()
    {
        $response = json_encode([
            'data' => ContactFactory::create(2),
            'info' => [
                'per_page' => 200,
                'count' => 2,
                'page' => 1,
                'more_records' => false
            ]
        ]);

        $api = Zoho::fake([TransactionMock::create($response)]);

        $response = $api->contacts->get();

        $this->assertEquals(200, $api->lastResponseStatusCode());
        $this->assertTrue(is_array($response));
        $this->assertEquals('GET', $api->lastRequestMethod());
        $this->assertEquals(sprintf('/%s/%s', Zoho::$base, 'Contacts'), $api->lastRequestUri());
        $this->assertCount(2, $response);
    }

    /**
     * GET /Contacts/123
     *
     * @test
     * @throws \ShineOnCom\Zoho\Exceptions\InvalidOrMissingEndpointException
     * @throws \ShineOnCom\Zoho\Exceptions\ModelNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function it_gets_a_contact_by_id()
    {
        $contact = ContactFactory::create();

        $response = json_encode(['data' => [$contact]]);

        $api = Zoho::fake([TransactionMock::create($response)]);

        $response = $api->contacts->find($contact['id']);

        $this->assertEquals(200, $api->lastResponseStatusCode());
        $this->assertEquals(Contact::class, get_class($response));
        $this->assertEquals('GET', $api->lastRequestMethod());
        $this->assertEquals(sprintf('/%s/Contacts/%d', Zoho::$base, $contact['id']), $api->lastRequestUri());
    }

    /**
     * POST /coql
     *
     * @test
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function it_gets_a_contact_by_email()
    {
        $contact = ContactFactory::create();

        $response = json_encode([
            'data' => [
                $contact
            ],
            'info' => [
                'count' => 1,
                'more_records' => false
            ]
        ]);

        $api = Zoho::fake([TransactionMock::create($response)]);

        $response = $api->contacts->findByEmail($contact['Email']);

        $this->assertEquals(200, $api->lastResponseStatusCode());
        $this->assertEquals(Contact::class, get_class($response));
        $this->assertEquals('POST', $api->lastRequestMethod());
        $this->assertEquals(sprintf('/%s/coql', Zoho::$base), $api->lastRequestUri());
    }
}