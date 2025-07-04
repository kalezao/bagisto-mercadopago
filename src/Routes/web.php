<?php

use Illuminate\Support\Facades\Route;
use kalezao\MercadoPagoPaymentMethod\Http\Controllers\MercadoPagoController;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('mercadopago')->group(function () {
        Route::get('success', [MercadoPagoController::class, 'success'])->name('mercadopago.success');
        Route::get('failure', [MercadoPagoController::class, 'failure'])->name('mercadopago.failure');
        Route::get('pending', [MercadoPagoController::class, 'pending'])->name('mercadopago.pending');
        Route::post('webhook', [MercadoPagoController::class, 'webhook'])
            ->middleware('mercadopago.webhook')
            ->name('mercadopago.webhook');
    });
}); 