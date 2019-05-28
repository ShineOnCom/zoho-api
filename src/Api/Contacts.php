<?php

namespace ShineOnCom\Zoho\Api;

use Exception;
use ShineOnCom\Zoho\Models\Contact;

/**
 * Class Contacts
 */
class Contacts extends Endpoint
{
    /**
     * Find a Contact by Email
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function findByEmail($email)
    {
        $select_query = sprintf('select %s from Contacts where Email = %s', implode(',', Contact::$fields), $email);

        $data = $this->client->query($select_query);

        if ($data['info']['count'] != 1) {
            throw new Exception('More than 1 record was found.');
        }

        return $this->client->getModel($data);
    }
}