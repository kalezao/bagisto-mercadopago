<div class="payment-method">
    <div class="payment-method-header">
        <div class="payment-method-title">
            <span class="payment-method-name">{{ $paymentMethod->getTitle() }}</span>
        </div>
        
        @if ($paymentMethod->getImage())
            <div class="payment-method-image">
                <img src="{{ $paymentMethod->getImage() }}" alt="{{ $paymentMethod->getTitle() }}" />
            </div>
        @endif
    </div>

    <div class="payment-method-content">
        <div class="payment-method-description">
            {{ $paymentMethod->getDescription() }}
        </div>

        @if ($paymentMethod->getAdditionalDetails())
            <div class="payment-method-additional">
                @foreach ($paymentMethod->getAdditionalDetails() as $detail)
                    <div class="payment-method-detail">
                        <span class="detail-title">{{ $detail['title'] }}:</span>
                        <span class="detail-value">{{ $detail['value'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="payment-method-instructions">
            <p>
                <strong>Como funciona:</strong>
                <ul>
                    <li>Clique em "Pagar com Mercado Pago"</li>
                    <li>Você será redirecionado para o site do Mercado Pago</li>
                    <li>Escolha sua forma de pagamento preferida</li>
                    <li>Após o pagamento, você será redirecionado de volta</li>
                </ul>
            </p>
        </div>
    </div>
</div>

<style>
.payment-method {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background: #fff;
}

.payment-method-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.payment-method-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.payment-method-image img {
    max-height: 40px;
    max-width: 120px;
}

.payment-method-content {
    color: #666;
}

.payment-method-description {
    margin-bottom: 15px;
    line-height: 1.5;
}

.payment-method-additional {
    margin-bottom: 15px;
}

.payment-method-detail {
    margin-bottom: 5px;
}

.detail-title {
    font-weight: 600;
    color: #333;
}

.payment-method-instructions {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}

.payment-method-instructions ul {
    margin: 10px 0 0 20px;
    padding: 0;
}

.payment-method-instructions li {
    margin-bottom: 5px;
    line-height: 1.4;
}
</style> 