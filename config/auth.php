<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    | Guard default tetap 'web' untuk admin.
    */

    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    | web       → admin (tabel users)
    | guru      → guru madrasah (tabel guru_users)
    | madrasah  → akun madrasah (tabel madrasah_users)
    */

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        'guru' => [
            'driver'   => 'session',
            'provider' => 'guru_users',
        ],

        'madrasah' => [
            'driver'   => 'session',
            'provider' => 'madrasah_users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],

        'guru_users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\GuruUser::class,
        ],

        'madrasah_users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\MadrasahUser::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
