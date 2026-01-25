<?php

return [
    'webhook' => [
        'throttle' => env('MERCHANT_WEBHOOK_THROTTLE', '60,1'),
    ],

    'sync' => [
        'http_timeout' => (int) env('MERCHANT_SYNC_HTTP_TIMEOUT', 15),
    ],
];
