<?php

namespace kalezao\MercadoPagoPaymentMethod\Payment;

use Webkul\Payment\Payment\Payment;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Common\RequestOptions;
use kalezao\MercadoPagoPaymentMethod\Helpers\MercadoPagoLogger;
use MercadoPago\Exceptions\MPApiException;

class MercadoPagoPaymentMethod extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code = 'mercadopago';

    /**
     * Mercado Pago Preference Client
     *
     * @var PreferenceClient
     */
    protected $preferenceClient;

    /**
     * Mercado Pago Payment Client
     *
     * @var PaymentClient
     */
    protected $paymentClient;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initializeSDK();
    }

    /**
     * Initialize Mercado Pago SDK
     */
    protected function initializeSDK()
    {
        $accessToken = $this->getConfigData('access_token');
        
        if ($accessToken) {
            MercadoPagoConfig::setAccessToken($accessToken);
            $this->preferenceClient = new PreferenceClient();
            $this->paymentClient = new PaymentClient();
        }
    }

    /**
     * Get redirect URL for Mercado Pago Checkout Pro
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        try {
            $cart = $this->getCart();
            
            if (!$cart) {
                throw new \Exception('Carrinho não encontrado');
            }

            // Prepare items for preference
            $items = [];
            foreach ($cart->items as $item) {
                $items[] = [
                    'title' => $item->name,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->price,
                    'currency_id' => $this->getCurrencyCode(),
                ];
            }

            // Add shipping cost if exists
            if ($cart->shipping_amount > 0) {
                $items[] = [
                    'title' => 'Frete',
                    'quantity' => 1,
                    'unit_price' => (float) $cart->shipping_amount,
                    'currency_id' => $this->getCurrencyCode(),
                ];
            }

            // Prepare preference data
            $preferenceData = [
                'items' => $items,
                'payer' => [
                    'name' => trim($cart->customer_first_name . ' ' . $cart->customer_last_name),
                    'email' => $cart->customer_email,
                ],
                'back_urls' => [
                    'success' => $this->getConfigData('success_url') ?: route('mercadopago.success'),
                    'failure' => $this->getConfigData('failure_url') ?: route('mercadopago.failure'),
                    'pending' => $this->getConfigData('pending_url') ?: route('mercadopago.pending')
                ],
                'auto_return' => 'approved',
                'external_reference' => (string) $cart->id,
                'notification_url' => $this->getConfigData('webhook_url') ?: route('mercadopago.webhook'),
                'expires' => true,
                'expiration_date_to' => now()->addHours(24)->toISOString(),
            ];

            // Log preference data for debugging
            MercadoPagoLogger::info('Creating preference with data: ' . json_encode($preferenceData, JSON_PRETTY_PRINT));
            
            // Validate URLs
            if (empty($preferenceData['back_urls']['success'])) {
                throw new \Exception('URL de sucesso não está definida');
            }

            // Add payer identification if billing address exists
            if ($cart->billing_address) {
                $preferenceData['payer']['identification'] = [
                    'type' => 'CPF',
                    'number' => $cart->billing_address->phone ?? '00000000000'
                ];
            }

            // Create preference using new API
            $preference = $this->preferenceClient->create($preferenceData);

            // Store preference ID in session
            session(['mercadopago_preference_id' => $preference->id]);

            // Return checkout URL
            $sandboxMode = $this->getConfigData('sandbox_mode');
            
            if ($sandboxMode) {
                // Para sandbox, usamos a URL de teste
                return 'https://www.mercadopago.com.br/checkout/v1/redirect?pref_id=' . $preference->id . '&test=true';
            } else {
                // Para produção, usamos a URL normal
                return 'https://www.mercadopago.com.br/checkout/v1/redirect?pref_id=' . $preference->id;
            }

        } catch (MPApiException $e) {
            MercadoPagoLogger::error('Error creating preference: ' . $e->getMessage() . ' - ' . $e->getStatusCode() . ' - ' . json_encode($e->getApiResponse()->getContent()));
            throw new \Exception('Erro ao processar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Get currency code
     *
     * @return string
     */
    protected function getCurrencyCode()
    {
        return 'BRL'; // Default to BRL for Brazil
    }

    /**
     * Process success callback
     *
     * @param array $data
     * @return bool
     */
    public function processSuccess($data)
    {
        try {
            $paymentId = $data['payment_id'] ?? null;
            $preferenceId = $data['preference_id'] ?? null;
            
            if (!$paymentId || !$preferenceId) {
                throw new \Exception('Dados de pagamento inválidos');
            }

            // Verify payment with Mercado Pago using new API
            $payment = $this->paymentClient->get($paymentId);
            
            if (!$payment) {
                throw new \Exception('Pagamento não encontrado');
            }

            // Check payment status
            if ($payment->status === 'approved') {
                return true;
            } elseif ($payment->status === 'pending') {
                // Payment is pending, you might want to handle this differently
                return true;
            } else {
                throw new \Exception('Pagamento não foi aprovado.');
            }

        } catch (\Exception $e) {
            MercadoPagoLogger::error('Ocorreu um erro ao processar o pagamento. Código 392');
            return false;
        }
    }

    /**
     * Process webhook
     *
     * @param array $data
     * @return bool
     */
    public function processWebhook($data)
    {
        try {
            $type = $data['type'] ?? null;
            $data = $data['data'] ?? null;

            if ($type !== 'payment' || !$data) {
                return false;
            }

            $paymentId = $data['id'] ?? null;
            
            if (!$paymentId) {
                return false;
            }

            // Get payment details using new API
            $payment = $this->paymentClient->get($paymentId);
            
            if (!$payment) {
                return false;
            }

            // Get order from external reference
            $orderRepository = app(OrderRepository::class);
            $invoiceRepository = app(InvoiceRepository::class);
            $refundRepository = app(RefundRepository::class);

            $order = $orderRepository->findByField('cart_id', $payment->external_reference)->first();
            
            if (!$order) {
                MercadoPagoLogger::error('Order not found for payment: ' . $paymentId);
                return false;
            }

            // Update order status based on payment status
            switch ($payment->status) {
                case 'approved':
                    $invoice = $invoiceRepository->create($order->id);      
                    break;
                    
                case 'pending':
                    $invoice = $invoiceRepository->create($order->id);
                    break;
                    
                case 'rejected':
                case 'cancelled':
                    $orderRepository->cancel($order->id);
                    break;
                    
                case 'refunded':
                    if ($order->canBeRefunded()) {
                        $refund = $refundRepository->create($order->id);
                    }
                    break;
                    
                default:
                    return false;
            }

            $order->save();

            return true;

        } catch (\Exception $e) {
            MercadoPagoLogger::error('Webhook processing error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get payment method additional information
     *
     * @return array
     */
    public function getAdditionalDetails()
    {
        $sandboxMode = $this->getConfigData('sandbox_mode');
        
        $details = parent::getAdditionalDetails();
        
        if ($sandboxMode) {
            $details[] = [
                'title' => 'Modo de Teste',
                'value' => 'Ativo - Use cartões de teste do Mercado Pago',
            ];
        }

        return $details;
    }
}