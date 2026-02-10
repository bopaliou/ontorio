<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Deployment Token
    |--------------------------------------------------------------------------
    |
    | This token is used to authenticate requests to the system maintenance
    | routes (migrations, cache clearing, etc.) in a shared hosting environment.
    |
    */

    'token' => env('DEPLOY_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Web migration endpoint
    |--------------------------------------------------------------------------
    |
    | This endpoint is intentionally disabled by default.
    | Enable it only for constrained environments where no CI/CD or SSH is
    | available, and only for the shortest time window possible.
    |
    */

    'allow_web_migrate' => (bool) env('DEPLOY_ALLOW_WEB_MIGRATE', false),

];
