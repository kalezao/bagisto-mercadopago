<?php

namespace kalezao\MercadoPagoPaymentMethod\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use kalezao\MercadoPagoPaymentMethod\Helpers\MercadoPagoLogger;

class ValidateWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only validate webhook routes
        if ($request->route()->getName() !== 'mercadopago.webhook') {
            return $next($request);
        }

        try {
            // Basic validation for webhook data
            $data = $request->all();
            
            if (empty($data)) {
                MercadoPagoLogger::warning('Webhook: Empty data received');
                return response('Invalid webhook data', 400);
            }

            // Check if required fields are present
            if (!isset($data['type']) || !isset($data['data'])) {
                MercadoPagoLogger::warning('Webhook: Missing required fields', $data);
                return response('Missing required fields', 400);
            }

            // Validate webhook type
            $validTypes = ['payment', 'preference', 'subscription'];
            if (!in_array($data['type'], $validTypes)) {
                MercadoPagoLogger::warning('Webhook: Invalid type', ['type' => $data['type']]);
                return response('Invalid webhook type', 400);
            }

            // Log webhook for debugging
            MercadoPagoLogger::webhook('Webhook received', [
                'type' => $data['type'],
                'data' => $data['data'],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $next($request);

        } catch (\Exception $e) {
            MercadoPagoLogger::error('Webhook validation error: ' . $e->getMessage());
            return response('Webhook validation error', 500);
        }
    }
} 