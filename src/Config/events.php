<?php

return [
    'listeners' => [
        'mercadopago.payment.success' => [
            'kalezao\MercadoPagoPaymentMethod\Listeners\PaymentSuccessListener',
        ],
        'mercadopago.payment.failed' => [
            'kalezao\MercadoPagoPaymentMethod\Listeners\PaymentFailedListener',
        ],
        'mercadopago.payment.pending' => [
            'kalezao\MercadoPagoPaymentMethod\Listeners\PaymentPendingListener',
        ],
    ],
]; 