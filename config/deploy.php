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

];
