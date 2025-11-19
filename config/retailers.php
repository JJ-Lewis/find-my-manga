<?php

return [
    'adapters' => [
        // key => class
        'test' => \App\Services\Retailer\Adapters\TestRetailerAdapter::class
    ],

    'defaults' => [
        'currency' => 'ZAR',
    ],

    // Example static config for BooksNow
    'test' => [
        'base_url' => env('TEST_BASE_URL', 'https://api.retailer.test'),
        'api_key'  => env('TEST_API_KEY', 'your-test-key'),
    ],
];
