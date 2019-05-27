<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Zoho API Access Token
    |--------------------------------------------------------------------------
    |
    | Use of a token implies you've already proceeding to Zoho's's Oauth flow
    | and have a token in your possession to make subsequent requests. See the
    | readme.md for help getting your token.
    */
    'token' => env('ZOHO_TOKEN', ''),
    // TODO: also add refresh_token env/config

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    | log_api_request_data:
    | When enabled will log the data of every API Request made to zoho' API.
    */
    'options' => [
        'log_api_request_data' => env('ZOHO_OPTION_LOG_API_REQUEST', 0),
    ],
];
