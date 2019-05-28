<?php

namespace ShineOnCom\Zoho\Api;

/**
 * Class Customers
 */
class Contacts extends Endpoint
{

    public function findByEmail()
    {
        echo __METHOD__;
        var_dump(func_get_args());
        exit();
    }
}