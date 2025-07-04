<?php

return [
    'jobs' => [
        'kalezao\MercadoPagoPaymentMethod\Jobs\ProcessMercadoPagoWebhook',
        'kalezao\MercadoPagoPaymentMethod\Jobs\SyncMercadoPagoPayment',
    ],
]; 