<?php

namespace kalezao\MercadoPagoPaymentMethod\Helpers;

use Illuminate\Support\Facades\Log;

class MercadoPagoLogger
{
    /**
     * Log info message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function info($message, array $context = [])
    {
        if (config('mercadopago.logging.log_payments', true)) {
            Log::channel('mercadopago')->info($message, $context);
        }
    }

    /**
     * Log error message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function error($message, array $context = [])
    {
        if (config('mercadopago.logging.log_errors', true)) {
            Log::channel('mercadopago')->error($message, $context);
        }
    }

    /**
     * Log warning message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function warning($message, array $context = [])
    {
        if (config('mercadopago.logging.log_errors', true)) {
            Log::channel('mercadopago')->warning($message, $context);
        }
    }

    /**
     * Log webhook message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function webhook($message, array $context = [])
    {
        if (config('mercadopago.logging.log_webhooks', true)) {
            Log::channel('mercadopago')->info('[WEBHOOK] ' . $message, $context);
        }
    }

    /**
     * Log payment message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function payment($message, array $context = [])
    {
        if (config('mercadopago.logging.log_payments', true)) {
            Log::channel('mercadopago')->info('[PAYMENT] ' . $message, $context);
        }
    }

    /**
     * Log API request/response
     *
     * @param string $method
     * @param string $url
     * @param array $request
     * @param array $response
     * @return void
     */
    public static function api($method, $url, array $request = [], array $response = [])
    {
        if (config('mercadopago.logging.log_payments', true)) {
            Log::channel('mercadopago')->info('[API] ' . $method . ' ' . $url, [
                'request' => $request,
                'response' => $response,
            ]);
        }
    }
} 