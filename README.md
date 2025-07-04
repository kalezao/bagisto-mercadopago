# Mercado Pago Payment Method para Bagisto

Este package integra o Mercado Pago Checkout Pro ao Bagisto, permitindo que os clientes realizem pagamentos de forma segura e rápida.

## Características

- ✅ Integração completa com Mercado Pago Checkout Pro
- ✅ Suporte a modo sandbox para testes
- ✅ Webhooks para atualização automática de status
- ✅ Callbacks de sucesso, falha e pendente
- ✅ Interface administrativa para configuração
- ✅ Suporte a múltiplas moedas (padrão BRL)
- ✅ Logs detalhados para debugging

## Requisitos

- PHP 8.2 ou superior
- Laravel 10+
- Bagisto 1.x
- Conta no Mercado Pago
- Mercado Pago SDK v3.5.1+

## Instalação

### 1. Instalar o Package

```bash
composer require kalezao/mercadopago-payment-method
composer require mercadopago/dx-php:^3.5
```

### 2. Publicar Assets (Opcional)

```bash
php artisan vendor:publish --tag=mercadopago-views
php artisan vendor:publish --tag=mercadopago-lang
```

### 3. Limpar Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Configuração

### 1. Configuração no Mercado Pago

1. Acesse sua conta no [Mercado Pago](https://www.mercadopago.com.br/)
2. Vá para **Desenvolvedores > Suas integrações**
3. Crie uma nova aplicação ou use uma existente
4. Copie as credenciais:
   - **Public Key**
   - **Access Token**

### 2. Configuração no Bagisto

1. Acesse o painel administrativo do Bagisto
2. Vá para **Configuração > Vendas > Métodos de Pagamento**
3. Procure por **Mercado Pago** e clique em **Configurar**
4. Preencha os campos:
   - **Título**: Nome que aparecerá para o cliente
   - **Descrição**: Descrição do método de pagamento
   - **URL da imagem**: Logo do Mercado Pago (opcional)
   - **Public Key**: Sua chave pública do Mercado Pago
   - **Access Token**: Seu token de acesso do Mercado Pago
   - **Modo Sandbox**: Ative para testes, desative para produção
   - **Ativo**: Ative o método de pagamento

### 3. Configuração de Webhooks

Para receber notificações automáticas de mudança de status:

1. No painel do Mercado Pago, vá para **Desenvolvedores > Webhooks**
2. Adicione uma nova URL de webhook:
   ```
   https://seudominio.com/mercadopago/webhook
   ```
3. Selecione os eventos:
   - `payment.created`
   - `payment.updated`

## Uso

### Para o Cliente

1. O cliente adiciona produtos ao carrinho
2. No checkout, seleciona **Mercado Pago** como método de pagamento
3. Clica em **Pagar com Mercado Pago**
4. É redirecionado para o site do Mercado Pago
5. Escolhe a forma de pagamento (cartão, boleto, PIX, etc.)
6. Completa o pagamento
7. É redirecionado de volta para o site com confirmação

### Para o Administrador

- Os pedidos são criados automaticamente após pagamento aprovado
- Status dos pedidos são atualizados via webhook
- Logs detalhados são salvos para debugging

## Modo Sandbox

Para testes, ative o **Modo Sandbox** nas configurações. Use os cartões de teste do Mercado Pago:

### Cartões de Teste

- **Aprovado**: 4509 9535 6623 3704
- **Pendente**: 4000 0000 0000 0002
- **Rejeitado**: 4000 0000 0000 0004

### Dados de Teste

- **CVV**: Qualquer número de 3 dígitos
- **Data de Vencimento**: Qualquer data futura
- **Nome**: Qualquer nome
- **CPF**: 12345678909

## Estrutura do Package

```
src/
├── Config/
│   ├── paymentmethods.php    # Configuração do método de pagamento
│   └── system.php           # Configuração do sistema
├── Http/
│   └── Controllers/
│       └── MercadoPagoController.php  # Controller para callbacks
├── Payment/
│   └── MercadoPagoPaymentMethod.php   # Classe principal
├── Providers/
│   └── MercadoPagoPaymentMethodServiceProvider.php  # Service Provider
├── Resources/
│   ├── lang/
│   │   └── pt_BR/
│   │       └── app.php      # Traduções
│   └── views/
│       └── checkout/
│           └── onepage/
│               └── payment-method.blade.php  # View do método
└── Routes/
    └── web.php              # Rotas do package
```

## Troubleshooting

### Problemas Comuns

1. **Erro "Carrinho não encontrado"**
   - Verifique se o carrinho está ativo
   - Limpe o cache da aplicação

2. **Erro "Dados de pagamento inválidos"**
   - Verifique se as credenciais do Mercado Pago estão corretas
   - Confirme se o Access Token está válido

3. **Webhooks não funcionando**
   - Verifique se a URL do webhook está acessível publicamente
   - Confirme se o SSL está configurado corretamente
   - Verifique os logs da aplicação

4. **Pagamento não aparece como aprovado**
   - Verifique se o webhook está configurado corretamente
   - Confirme se o Access Token tem permissões suficientes

### Logs

Os logs são salvos em:
```
storage/logs/laravel.log
```

Procure por entradas com "Mercado Pago" para debugging.

## Suporte

Para suporte técnico:
- Email: contato@kalezao.com
- GitHub: [Issues](https://github.com/kalezao/mercadopago-payment-method/issues)

## Licença

Este package é licenciado sob a MIT License.

## Changelog

### v1.0.0
- Integração inicial com Mercado Pago Checkout Pro
- Suporte a callbacks de sucesso, falha e pendente
- Webhooks para atualização automática de status
- Interface administrativa completa
- Modo sandbox para testes 