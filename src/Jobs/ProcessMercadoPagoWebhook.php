<?php

namespace kalezao\MercadoPagoPaymentMethod\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use kalezao\MercadoPagoPaymentMethod\Helpers\MercadoPagoLogger;
use Webkul\Sales\Repositories\OrderRepository;

class ProcessMercadoPagoWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The webhook data.
     *
     * @var array
     */
    protected $webhookData;

    /**
     * Create a new job instance.
     *
     * @param array $webhookData
     * @return void
     */
    public function __construct(array $webhookData)
    {
        $this->webhookData = $webhookData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            MercadoPagoLogger::info('Processing webhook in queue', $this->webhookData);

            $type = $this->webhookData['type'] ?? null;
            $data = $this->webhookData['data'] ?? null;

            if ($type !== 'payment' || !$data) {
                MercadoPagoLogger::warning('Invalid webhook data received', $this->webhookData);
                return;
            }

            $paymentId = $data['id'] ?? null;
            
            if (!$paymentId) {
                MercadoPagoLogger::warning('Payment ID not found in webhook data', $this->webhookData);
                return;
            }

            // Get payment details from Mercado Pago using new API
            $paymentClient = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $paymentClient->get($paymentId);
            
            if (!$payment) {
                MercadoPagoLogger::error('Payment not found in Mercado Pago', ['payment_id' => $paymentId]);
                return;
            }

            // Find order by external reference
            $orderRepository = app(OrderRepository::class);
            $order = $orderRepository->findByField('cart_id', $payment->external_reference)->first();
            
            if (!$order) {
                MercadoPagoLogger::error('Order not found for payment', [
                    'payment_id' => $paymentId,
                    'external_reference' => $payment->external_reference
                ]);
                return;
            }

            // Update order status based on payment status
            $this->updateOrderStatus($order, $payment);

            MercadoPagoLogger::info('Webhook processed successfully', [
                'payment_id' => $paymentId,
                'order_id' => $order->id,
                'status' => $payment->status
            ]);

        } catch (\Exception $e) {
            MercadoPagoLogger::error('Error processing webhook: ' . $e->getMessage(), [
                'webhook_data' => $this->webhookData,
                'exception' => $e
            ]);
            
            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Update order status based on payment status.
     *
     * @param \Webkul\Sales\Contracts\Order $order
     * @param \MercadoPago\Payment $payment
     * @return void
     */
    protected function updateOrderStatus($order, $payment)
    {
        switch ($payment->status) {
            case 'approved':
                $order->status = 'processing';
                $order->payment_status = 'paid';
                break;
                
            case 'pending':
                $order->status = 'pending';
                $order->payment_status = 'pending';
                break;
                
            case 'rejected':
            case 'cancelled':
                $order->status = 'canceled';
                $order->payment_status = 'failed';
                break;
                
            default:
                                MercadoPagoLogger::warning('Unknown payment status', [
                    'payment_id' => $payment->id,
                    'status' => $payment->status
                ]);
                return;
            }

            $order->save();
    }
} 