<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Short URL Domain
    |--------------------------------------------------------------------------
    |
    | This value is the domain that will be used for shortened URLs. This should
    | be set to your application's domain where the shortened URLs will be 
    | accessible.
    |
    */
    'short_url_prefix' => env('SHORT_URL_PREFIX', 'https://short.test'),

    /*
    |--------------------------------------------------------------------------
    | Code Generation Settings
    |--------------------------------------------------------------------------
    |
    | These settings control how short codes are generated:
    | - allowed_chars: Characters that can be used in generated codes
    |   (excludes vowels to avoid accidental words)
    | - length: Length of generated codes
    |
    */
    'code_generation' => [
        'allowed_chars' => 'bcdfghjkmpqrtvwxyBCDFGHJKMPQRTVWXY346789',
        'length' => 6,
    ],
];