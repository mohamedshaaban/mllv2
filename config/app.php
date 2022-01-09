<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),
    'XERO_TENANT_ID' => env('XERO_TENANT_ID', '605e8ed9-11e4-4e83-98bc-3c56047940e0'),
    'XEROTOKEN' => env('XEROTOKEN', 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjFDQUY4RTY2NzcyRDZEQzAyOEQ2NzI2RkQwMjYxNTgxNTcwRUZDMTkiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJISy1PWm5jdGJjQW8xbkp2MENZVmdWY09fQmsifQ.eyJuYmYiOjE2MzM3OTM4NzksImV4cCI6MTYzMzc5NTY3OSwiaXNzIjoiaHR0cHM6Ly9pZGVudGl0eS54ZXJvLmNvbSIsImF1ZCI6Imh0dHBzOi8vaWRlbnRpdHkueGVyby5jb20vcmVzb3VyY2VzIiwiY2xpZW50X2lkIjoiRUU0RTQ5OTZFNkQyNDU5MzkxMkYyQ0RCRUQzMUNCNzQiLCJzdWIiOiI1NDdmNGE5ZGUyNDk1YmFkOWRiZDdhZTcxMWViMDVhMCIsImF1dGhfdGltZSI6MTYzMzc5MDIyMiwieGVyb191c2VyaWQiOiI3YTQ3ODhiNy1hYWU2LTQ3NzEtOGIyMi05NDViNWIxN2IxNzAiLCJnbG9iYWxfc2Vzc2lvbl9pZCI6IjQ3OWIzN2RkYThiYTRlYThhNDVhNjI2MzQ4MTFhZGU4IiwianRpIjoiM2U0YjQxOTY4MWE5ZTQ4MTMxNTQ3MDk3NjdlZDAxOWEiLCJhdXRoZW50aWNhdGlvbl9ldmVudF9pZCI6IjgyMzk0Zjg5LTE5NzgtNGU0OS1iMjRmLWU1OTJkNTA5NTA1NSIsInNjb3BlIjpbIm9wZW5pZCIsIm9mZmxpbmVfYWNjZXNzIl19.dUOQ5TrBhneeTvgejQttlA30F9D6Ur41D9B4d8BHflezDsf-Z7OY89Y_3wmGJOLhV2EVfmdu_uptM5K1GK6UeGeNQjzPtB5VEdeo3g9my8lLeV0Onb7FMh_7gTu3KT8HuEJ2qgMX8joC9iexieFZLHxfKfjLNogTYsi3UiJ0JKqCgtkFAafXjhEe27F5yXJROwJVHXozWgM8dr9SNYjDQpLsnu8zAtyBNBLd0vZ3gwxakJtzKoqqy-aWXWS7ASF4rH2x9wvBggRj3ITB52NrxEU5QT30njEKnEZfNSFUcn6uJ0kW6WbnxxuUK9QDT7ufVeAHdoIAEd-SUpW4SQ5BkQ'),
    'XEROREFRESHTOKEN' => env('XEROREFRESHTOKEN', 'ec3cbf466267226488d78a83b16880bc339e4dc07266f9e03577a1460cdae46d'),
    'XEROKNET' => env('XEROKNET', 2),
    'XEROCASH' => env('XEROCASH', 92),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),
    'TAPPAYMENT_SecretAPIKey'=>env('TAPPAYMENT_SecretAPIKey','sk_live_QDEw2a4CpTHFAm8dZcW0nkMR'),
    'TAPPAYMENT_PublishableAPIKey'=>env('TAPPAYMENT_PublishableAPIKey','pk_live_0qgeG13ZdRUhIJk9YLWuFxVE'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Kuwait',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => env('APP_LOG', 'daily'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\HelperServiceProvider::class,
        DrawMyAttention\XeroLaravel\Providers\XeroServiceProvider::class,

        /*
        * Other Service Providers...
        */

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App'       => Illuminate\Support\Facades\App::class,
        'Arr'       => Illuminate\Support\Arr::class,
        'Artisan'   => Illuminate\Support\Facades\Artisan::class,
        'Auth'      => Illuminate\Support\Facades\Auth::class,
        'Blade'     => Illuminate\Support\Facades\Blade::class,
        'Cache'     => Illuminate\Support\Facades\Cache::class,
        'Config'    => Illuminate\Support\Facades\Config::class,
        'Cookie'    => Illuminate\Support\Facades\Cookie::class,
        'Crypt'     => Illuminate\Support\Facades\Crypt::class,
        'DB'        => Illuminate\Support\Facades\DB::class,
        'Eloquent'  => Illuminate\Database\Eloquent\Model::class,
        'Event'     => Illuminate\Support\Facades\Event::class,
        'File'      => Illuminate\Support\Facades\File::class,
        'Gate'      => Illuminate\Support\Facades\Gate::class,
        'Hash'      => Illuminate\Support\Facades\Hash::class,
        'Lang'      => Illuminate\Support\Facades\Lang::class,
        'Log'       => Illuminate\Support\Facades\Log::class,
        'Mail'      => Illuminate\Support\Facades\Mail::class,
        'Password'  => Illuminate\Support\Facades\Password::class,
        'Queue'     => Illuminate\Support\Facades\Queue::class,
        'Redirect'  => Illuminate\Support\Facades\Redirect::class,
        'Redis'     => Illuminate\Support\Facades\Redis::class,
        'Request'   => Illuminate\Support\Facades\Request::class,
        'Response'  => Illuminate\Support\Facades\Response::class,
        'Route'     => Illuminate\Support\Facades\Route::class,
        'Schema'    => Illuminate\Support\Facades\Schema::class,
        'Session'   => Illuminate\Support\Facades\Session::class,
        'Storage'   => Illuminate\Support\Facades\Storage::class,
        'Str'       => Illuminate\Support\Str::class,
        'URL'       => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View'      => Illuminate\Support\Facades\View::class,
        'XeroPrivate'=> 'DrawMyAttention\XeroLaravel\Facades\XeroPrivate',

        /*
        * Other Aliases...
        */

    ],

];
