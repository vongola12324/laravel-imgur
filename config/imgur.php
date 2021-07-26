<?php

return [
    /**
     * Client Application Client_id, Client Secret
     * You need to add client_id, client_secret in .env file, or client will not be able to work.
     * For more information, please visit https://apidocs.imgur.com/
     */
    'client_id' => env('IMGUR_CLIENT_ID', null),
    'client_secret' => env('IMGUR_CLIENT_SECRET', null),
];
