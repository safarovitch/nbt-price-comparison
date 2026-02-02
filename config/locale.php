<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | This is the default locale that will be used when no locale prefix
    | is present in the URL. This locale will not have a prefix in URLs.
    |
    */

    'default_locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | List of all supported locales. The default locale should be included
    | in this array. Other locales will have /{locale}/ prefix in URLs.
    |
    */

    'supported_locales' => explode(',', env('APP_SUPPORTED_LOCALES', 'en,ru,tg')),

    /*
    |--------------------------------------------------------------------------
    | Locale Names
    |--------------------------------------------------------------------------
    |
    | Human-readable names for each locale used in the language switcher.
    |
    */

    'locale_names' => [
        'en' => 'English',
        'ru' => 'Русский',
        'tg' => 'Тоҷикӣ',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hide Default Locale in URL
    |--------------------------------------------------------------------------
    |
    | When true, the default locale will not have a prefix in URLs.
    | Example: /about instead of /en/about
    |
    */

    'hide_default_locale_in_url' => env('APP_HIDE_DEFAULT_LOCALE', false),
];
