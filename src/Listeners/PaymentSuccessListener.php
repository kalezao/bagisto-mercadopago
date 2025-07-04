<?php

namespace kalezao\MercadoPagoPaymentMethod\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentSuccessListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try {
            $order = $event->order;
            $payment = $event->payment;

            Log::info('Mercado Pago Payment Success', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'amount' => $payment->transaction_amount,
                'status' => $payment->status,
            ]);

            // Send confirmation email to customer
            if ($order->customer_email) {
                // You can implement email sending here
                // Mail::to($order->customer_email)->send(new PaymentConfirmationMail($order));
            }

            // Send notification to admin
            // You can implement admin notification here

        } catch (\Exception $e) {
            Log::error('Mercado Pago Payment Success Listener Error: ' . $e->getMessage());
        }
    }
} 