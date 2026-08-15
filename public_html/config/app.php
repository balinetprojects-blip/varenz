<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Varenz Supplies Ltd — Application Configuration
|--------------------------------------------------------------------------
|
| Central configuration for the existing Varenz public website.
|
| This file deliberately contains configuration only. It must not render
| markup, perform routing, alter the existing UI structure or contain
| frontend business logic.
|
| Security rules:
|
| - Production debugging can never be enabled accidentally.
| - Environment values are allow-listed.
| - APP_URL must be a valid HTTP/HTTPS URL.
| - Private storage stays outside /assets.
| - Contact information remains centrally managed.
| - Upload limits remain centrally controlled.
|
*/


/*
|--------------------------------------------------------------------------
| Environment
|--------------------------------------------------------------------------
*/

$environment = strtolower(
    trim(
        (string) (
            getenv('APP_ENV')
            ?: 'production'
        )
    )
);

$allowedEnvironments = [
    'production',
    'staging',
    'development',
    'testing',
];

if (
    !in_array(
        $environment,
        $allowedEnvironments,
        true
    )
) {
    $environment = 'production';
}


/*
|--------------------------------------------------------------------------
| Debug Mode
|--------------------------------------------------------------------------
|
| APP_DEBUG is deliberately ignored in production.
|
*/

$requestedDebug = filter_var(
    getenv('APP_DEBUG') ?: '0',
    FILTER_VALIDATE_BOOL,
    FILTER_NULL_ON_FAILURE
) === true;

$debug =
    $environment !== 'production'
    && $requestedDebug;


/*
|--------------------------------------------------------------------------
| Base URL
|--------------------------------------------------------------------------
|
| Recommended production value:
|
| APP_URL=https://varenzsupplies.com
|
| Leaving APP_URL blank is valid. In that case bootstrap.php derives the
| installation path from the request.
|
*/

$rawBaseUrl = trim(
    (string) (
        getenv('APP_URL')
        ?: ''
    )
);

$baseUrl = '';

if ($rawBaseUrl !== '') {
    /*
     * Strip control characters before URL validation.
     */
    $rawBaseUrl = preg_replace(
        '/[\x00-\x1F\x7F]/',
        '',
        $rawBaseUrl
    ) ?? '';

    if ($rawBaseUrl !== '') {
        $validatedBaseUrl = filter_var(
            $rawBaseUrl,
            FILTER_VALIDATE_URL
        );

        if ($validatedBaseUrl !== false) {
            $scheme = strtolower(
                (string) parse_url(
                    $validatedBaseUrl,
                    PHP_URL_SCHEME
                )
            );

            $host = trim(
                (string) parse_url(
                    $validatedBaseUrl,
                    PHP_URL_HOST
                )
            );

            if (
                in_array(
                    $scheme,
                    [
                        'http',
                        'https',
                    ],
                    true
                )
                && $host !== ''
            ) {
                $baseUrl = rtrim(
                    $validatedBaseUrl,
                    '/'
                );
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Private Storage
|--------------------------------------------------------------------------
*/

$storagePath =
    dirname(__DIR__)
    . '/storage/private';


/*
|--------------------------------------------------------------------------
| Upload Policy
|--------------------------------------------------------------------------
*/

$uploadMaxBytes =
    8 * 1024 * 1024;

$allowedUploadExtensions = [
    'pdf',

    'doc',
    'docx',

    'xls',
    'xlsx',

    'csv',

    'jpg',
    'jpeg',
    'png',
    'webp',
];


/*
|--------------------------------------------------------------------------
| Final Configuration
|--------------------------------------------------------------------------
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Application
    |--------------------------------------------------------------------------
    */

    'name' =>
        'Varenz Supplies Ltd',

    'timezone' =>
        'Africa/Kampala',

    'environment' =>
        $environment,

    'debug' =>
        $debug,

    /*
     * When empty, bootstrap.php resolves the application base path from
     * the current request.
     */
    'base_url' =>
        $baseUrl,


    /*
    |--------------------------------------------------------------------------
    | Private Storage
    |--------------------------------------------------------------------------
    |
    | Submission records, upload files and rate-limit state belong here.
    |
    | Nothing stored here should be exposed through /assets.
    |
    */

    'storage_path' =>
        $storagePath,


    /*
    |--------------------------------------------------------------------------
    | Public Contact Information
    |--------------------------------------------------------------------------
    |
    | This remains the authoritative source used by the homepage, footer,
    | contact shortcuts and other public interfaces.
    |
    */

    'contact' => [

        'phone_primary' =>
            '0701165527',

        'phone_secondary' =>
            '0730850411',

        'email' =>
            'info@varenzsupplies.com',

        'location' =>
            'Komamboga, Kampala, Uganda',

        'hours' =>
            'Monday-Saturday, 8:00 AM-6:00 PM',

    ],


    /*
    |--------------------------------------------------------------------------
    | Request Attachment Policy
    |--------------------------------------------------------------------------
    |
    | SubmissionRepository remains responsible for authoritative MIME,
    | extension, upload-integrity and filesystem validation.
    |
    | Browser accept="" attributes are usability hints only.
    |
    */

    'upload' => [

        'max_bytes' =>
            $uploadMaxBytes,

        'allowed_extensions' =>
            $allowedUploadExtensions,

    ],

];