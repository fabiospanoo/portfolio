<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin credentials
    |--------------------------------------------------------------------------
    */

    'password' => env('ADMIN_PASSWORD'),

    'password_hash' => env('ADMIN_PASSWORD_HASH'),

    'totp_holder' => env('ADMIN_TOTP_HOLDER', 'fabiospanoo@outlook.it'),

    'totp_issuer' => env('ADMIN_TOTP_ISSUER', 'Fabio Spano Portfolio'),

];
