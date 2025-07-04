<?php

namespace kalezao\MercadoPagoPaymentMethod\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class MercadoPagoPaymentMethodServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'mercadopago');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'mercadopago');
        
        // Register middleware
        $router->aliasMiddleware('mercadopago.webhook', \kalezao\MercadoPagoPaymentMethod\Http\Middleware\ValidateWebhook::class);
        
        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('views/vendor/mercadopago'),
        ], 'mercadopago-views');
        
        $this->publishes([
            __DIR__ . '/../Resources/lang' => resource_path('lang/vendor/mercadopago'),
        ], 'mercadopago-lang');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \kalezao\MercadoPagoPaymentMethod\Console\Commands\TestMercadoPagoConnection::class,
                \kalezao\MercadoPagoPaymentMethod\Console\Commands\TestMercadoPagoLogger::class,
            ]);
        }

        // Register custom log channel for Mercado Pago
        $this->registerMercadoPagoLogChannel();
    }

    /**
     * Register custom log channel for Mercado Pago
     */
    protected function registerMercadoPagoLogChannel()
    {
        $this->app['config']->set('logging.channels.mercadopago', [
            'driver' => 'daily',
            'path' => storage_path('logs/mercadopago.log'),
            'level' => config('mercadopago.logging.level', 'info'),
            'days' => config('mercadopago.logging.max_files', 30),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }
    
    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php', 'payment_methods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php', 'acl'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/events.php', 'events'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/validations.php', 'validations'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/middleware.php', 'middleware'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/commands.php', 'commands'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/queue.php', 'queue'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/cache.php', 'cache'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/logging.php', 'logging'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/notifications.php', 'notifications'
        );
    }
}
