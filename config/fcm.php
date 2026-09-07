<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging (FCM) Credentials & Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the credentials for Firebase Cloud Messaging.
    | These values can be populated from your .env file or dynamically
    | managed and customized via the Admin Settings Dashboard.
    |
    */

    // Legacy Server Key / API Key for FCM HTTP Dispatch
    'server_key' => env('FIREBASE_SERVER_KEY', ''),

    // Firebase Web SDK Configuration
    'api_key' => env('FIREBASE_API_KEY', 'AIzaSyCRAdcHPrsQOtHJGW7gMY9ZN1WiIOi8ClU'),
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN', 'bluezone-998e6.firebaseapp.com'),
    'project_id' => env('FIREBASE_PROJECT_ID', 'bluezone-998e6'),
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'bluezone-998e6.firebasestorage.app'),
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', '440590917450'),
    'app_id' => env('FIREBASE_APP_ID', '1:440590917450:web:9e289f9a0a36b2f10d2bb3'),
    'measurement_id' => env('FIREBASE_MEASUREMENT_ID', 'G-JKHXY1LDD8'),

    // Web Push VAPID Public Key Pair
    'vapid_key' => env('FIREBASE_VAPID_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Real-Time Notification Event Triggers
    |--------------------------------------------------------------------------
    |
    | Define which operational events automatically trigger real-time push
    | notifications to admin devices.
    |
    */
    'triggers' => [
        'low_stock' => env('FCM_TRIGGER_LOW_STOCK', true),
        'out_stock' => env('FCM_TRIGGER_OUT_STOCK', true),
        'transfers' => env('FCM_TRIGGER_TRANSFERS', true),
        'issues' => env('FCM_TRIGGER_ISSUES', true),
        'new_orders' => env('FCM_TRIGGER_ORDERS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Acoustic Feedback (Audio Chimes)
    |--------------------------------------------------------------------------
    */
    'sound_enabled' => env('FCM_SOUND_ENABLED', true),

];
