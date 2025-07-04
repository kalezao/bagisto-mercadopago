# Changelog

## [1.1.0] - 2024-01-XX

### Atualizado
- Migração para Mercado Pago SDK v3.5.1 (versão mais recente)
- Atualização da API para usar `MercadoPagoConfig`, `PreferenceClient` e `PaymentClient`
- Remoção de dependências obsoletas (`SDK`, `Preference`, `Item`, `Payer`)
- Melhoria na compatibilidade com PHP 8.2+

### Mudanças na API
- `SDK::setAccessToken()` → `MercadoPagoConfig::setAccessToken()`
- `new Preference()` → `new PreferenceClient()`
- `new Payment()` → `new PaymentClient()`
- `$preference->save()` → `$preferenceClient->create($data)`
- `Payment::find_by_id()` → `$paymentClient->get()`

### Compatibilidade
- PHP >= 8.2
- Laravel 10+
- Bagisto 1.x

## [1.0.0] - 2024-01-XX

### Adicionado
- Integração inicial com Mercado Pago Checkout Pro
- Suporte a callbacks de sucesso, falha e pendente
- Webhooks para atualização automática de status
- Interface administrativa completa
- Modo sandbox para testes
- Comandos Artisan para teste de conexão
- Jobs em fila para processamento de webhooks
- Middleware para validação de webhooks
- Sistema de logs e notificações
- Traduções em português brasileiro 