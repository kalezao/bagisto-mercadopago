<?php

return [
    'prefix' => 'mercadopago',
    'ttl' => [
        'preference' => 3600, // 1 hour
        'payment' => 1800,    // 30 minutes
        'webhook' => 300,     // 5 minutes
    ],
]; 