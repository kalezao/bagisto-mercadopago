<?php

return [
    'email' => [
        'payment_success' => true,
        'payment_failed' => true,
        'payment_pending' => false,
        'webhook_received' => false,
    ],
    'admin' => [
        'payment_success' => true,
        'payment_failed' => true,
        'webhook_error' => true,
    ],
    'channels' => [
        'mail',
        'database',
    ],
]; 