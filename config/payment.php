<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment gateway used by the storefront.
    | Administrators can override this in the Admin Settings panel.
    |
    */

    'default' => env('PAYMENT_DEFAULT_GATEWAY', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Currency Code
    |--------------------------------------------------------------------------
    |
    | The default three-letter ISO currency code for transactions.
    |
    */

    'currency' => env('CASHIER_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways Registry
    |--------------------------------------------------------------------------
    |
    | Configuration profiles for each supported payment gateway.
    | All parameters can be dynamically overridden by database settings
    | managed via the Admin Dashboard.
    |
    */

    'gateways' => [

        'stripe' => [
            'name' => 'Credit / Debit Card (Stripe)',
            'driver' => \App\Services\Payment\StripeGateway::class,
            'enabled' => env('STRIPE_ENABLED', true),
            'mode' => env('STRIPE_MODE', 'test'), // 'test' or 'live'
            'public_key' => env('STRIPE_KEY', 'pk_test_51MockStripeKeyBlueZoneLongevityDemo'),
            'secret_key' => env('STRIPE_SECRET', 'sk_test_51MockStripeSecretBlueZoneLongevityDemo'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', 'whsec_mockBlueZoneWebhookSecret2026'),
            'webhook_url' => env('STRIPE_WEBHOOK_URL', '/webhooks/payment/stripe'),
        ],

        'cod' => [
            'name' => 'Cash on Delivery (COD)',
            'driver' => \App\Services\Payment\CodGateway::class,
            'enabled' => env('COD_ENABLED', true),
            'extra_fee' => (float) env('COD_EXTRA_FEE', 0.00),
            'instruction' => 'Pay securely upon arrival with white-glove cold-chain courier.',
        ],

    ],

];
