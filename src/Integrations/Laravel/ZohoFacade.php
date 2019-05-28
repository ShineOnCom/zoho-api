<?php

namespace ShineOnCom\Zoho\Integrations\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * Class ZohoFacade
 *
 * Facade for the Laravel Framework
 */
class ZohoFacade extends Facade
{
    /**
     * Return \ShineOnCom\Zoho\Manager singleton.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'Zoho'; }
}