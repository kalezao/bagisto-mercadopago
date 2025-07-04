<?php

namespace kalezao\MercadoPagoPaymentMethod\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use kalezao\MercadoPagoPaymentMethod\Helpers\MercadoPagoLogger;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use kalezao\MercadoPagoPaymentMethod\Payment\MercadoPagoPaymentMethod;

class MercadoPagoController extends Controller
{
    /**
     * Success callback
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(Request $request)
    {
        try {
            $paymentMethod = new MercadoPagoPaymentMethod();
            
            $data = [
                'payment_id' => $request->get('payment_id'),
                'preference_id' => $request->get('preference_id'),
                'status' => $request->get('status'),
            ];

            if ($paymentMethod->processSuccess($data)) {
                // Get cart and create order
                $cart = Cart::getCart();
                
                if ($cart) {
                    $orderRepository = app(OrderRepository::class);
                    $order = $orderRepository->create($cart);
                    
                    if ($order) {
                        // Store order ID in session
                        session(['order_id' => $order->id]);
                        
                        // Clear cart
                        Cart::deActivateCart();
                        
                        return redirect()->route('shop.checkout.onepage.success');
                    }
                }
            }

            // If something went wrong, redirect to cart
            session()->flash('error', 'Erro ao processar pagamento. Tente novamente.');
            return redirect()->route('shop.checkout.cart.index');

        } catch (\Exception $e) {
            MercadoPagoLogger::error('Success callback error: ' . $e->getMessage());
            session()->flash('error', 'Erro ao processar pagamento: ' . $e->getMessage());
            return redirect()->route('shop.checkout.cart.index');
        }
    }

    /**
     * Failure callback
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function failure(Request $request)
    {
        $error = $request->get('error') ?? 'Pagamento foi rejeitado';
        
        session()->flash('error', 'Pagamento falhou: ' . $error);
        
        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Pending callback
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pending(Request $request)
    {
        session()->flash('info', 'Pagamento está pendente. Você será notificado quando for processado.');
        
        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Webhook callback
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        try {
            $paymentMethod = new MercadoPagoPaymentMethod();
            
            $data = $request->all();
            
            if ($paymentMethod->processWebhook($data)) {
                return response('OK', 200);
            }
            
            return response('Error processing webhook', 400);

        } catch (\Exception $e) {
            MercadoPagoLogger::error('Webhook error: ' . $e->getMessage());
            return response('Error: ' . $e->getMessage(), 500);
        }
    }
} 