<?php

return [
    'webhook' => [
        'throttle' => env('ORGANIZATION_WEBHOOK_THROTTLE', '60,1'),
    ],

    'sync' => [
        'http_timeout' => (int) env('ORGANIZATION_SYNC_HTTP_TIMEOUT', 15),
    ],
];
