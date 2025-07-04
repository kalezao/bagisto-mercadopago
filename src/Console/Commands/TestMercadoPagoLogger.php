<?php

namespace kalezao\MercadoPagoPaymentMethod\Console\Commands;

use Illuminate\Console\Command;
use kalezao\MercadoPagoPaymentMethod\Helpers\MercadoPagoLogger;

class TestMercadoPagoLogger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mercadopago:test-logger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Mercado Pago logger functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing Mercado Pago Logger...');

        try {
            // Test different log levels
            MercadoPagoLogger::info('Test info message', ['test' => 'info']);
            MercadoPagoLogger::error('Test error message', ['test' => 'error']);
            MercadoPagoLogger::warning('Test warning message', ['test' => 'warning']);
            MercadoPagoLogger::webhook('Test webhook message', ['test' => 'webhook']);
            MercadoPagoLogger::payment('Test payment message', ['test' => 'payment']);
            MercadoPagoLogger::api('GET', '/test', ['param' => 'value'], ['response' => 'success']);

            $this->info('✅ Logger test completed successfully!');
            $this->info('Check the log file at: storage/logs/mercadopago.log');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Logger test failed: ' . $e->getMessage());
            return 1;
        }
    }
} 