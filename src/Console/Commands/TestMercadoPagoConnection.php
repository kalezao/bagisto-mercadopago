<?php

namespace kalezao\MercadoPagoPaymentMethod\Console\Commands;

use Illuminate\Console\Command;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;

class TestMercadoPagoConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mercadopago:test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connection with Mercado Pago API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing Mercado Pago connection...');

        // Test API connection by trying to get a payment (will fail but shows connection works)
        try {
            // Get access token from config
            $accessToken = core()->getConfigData('sales.payment_methods.mercadopago.access_token');
            
            if (!$accessToken) {
                $this->error('Access Token not configured. Please configure Mercado Pago in admin panel.');
                return 1;
            }

            // Set access token
            MercadoPagoConfig::setAccessToken($accessToken);
            $paymentClient = new PaymentClient();

            $payment = $paymentClient->get('123456789');

        } catch (MPApiException $e) {
            // This is expected to fail, but it means the connection is working
            if ($e->getStatusCode() == 404) {
                $this->info('✅ Connection successful! API is responding.');
                
                // Check sandbox mode
                $sandboxMode = core()->getConfigData('sales.payment_methods.mercadopago.sandbox_mode');
                $mode = $sandboxMode ? 'Sandbox' : 'Production';
                $this->info("📋 Mode: {$mode}");
                
                // Check if method is active
                $active = core()->getConfigData('sales.payment_methods.mercadopago.active');
                $status = $active ? 'Active' : 'Inactive';
                $this->info("🔧 Status: {$status}");
                
                return 0;
            } else {
                throw $e;
            }
        }
    }
} 