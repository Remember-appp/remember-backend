<?php

use Knuckles\Scribe\Config\AuthIn;

return [
    'type' => 'laravel',

    'base_url' => config('app.url'),

    'routes' => [
        [
            'match' => [
                // Must be a path pattern
                'prefixes' => ['api/*'],
                'domains'  => ['*'],
            ],
            'include' => [
                // Keep empty unless you want to force-include named routes
            ],
            'exclude' => [
                // Keep empty
            ],
        ],
    ],

    'laravel' => [
        'add_routes' => true,
        'docs_url'   => '/docs',
        'middleware' => [],
    ],

    // Enable the "Authorize" button as Bearer
    'auth' => [
        'enabled'    => true,
        'default'    => false,
        'in'         => AuthIn::BEARER->value,
        'name'       => 'Authorization',
        'use_value'  => env('SCRIBE_AUTH_KEY', 'Bearer {token}'),
        'placeholder'=> 'Bearer {token}',
        'extra_info' => '',
    ],

    // Leave Postman/OpenAPI enabled; for "laravel" type they go to storage/app/scribe and are served at /docs.postman and /docs.openapi
    'postman' => [
        'enabled' => true,
        'overrides' => [],
    ],
    'openapi' => [
        'enabled' => true,
        'overrides' => [],
        'generators' => [],
    ],
];
