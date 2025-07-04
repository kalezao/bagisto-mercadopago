<?php

return [
    [
        'key'    => 'sales.payment_methods.mercadopago',
        'name'   => 'Pagamento via Mercado Pago',
        'info'   => 'Utilize a API do MercadoPago para permitir que o cliente realize suas compras.',
        'description' => 'Utilize a API do MercadoPago para permitir que o cliente realize suas compras.',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'Título',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'Descrição',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ],[
                'name'          => 'image',
                'title'         => 'URL da imagem',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => false,
                'validation'    => 'url',

            ], [
                'name'          => 'public_key',
                'title'         => 'Public Key',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
            ], [
                'name'          => 'access_token',
                'title'         => 'Access Token',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
            ], [
                'name'          => 'sandbox_mode',
                'title'         => 'Modo Sandbox',
                'type'          => 'boolean',
                'channel_based' => false,
                'locale_based'  => false,
            ], [
                'name'          => 'active',
                'title'         => 'Ativo',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'success_url',
                'title'         => 'URL de Sucesso',
                'type'          => 'text',
                'validation'    => 'required|url',
                'channel_based' => false,
                'locale_based'  => false,
                'info'          => 'URL para onde o cliente será redirecionado após pagamento aprovado',
            ], [
                'name'          => 'failure_url',
                'title'         => 'URL de Falha',
                'type'          => 'text',
                'validation'    => 'required|url',
                'channel_based' => false,
                'locale_based'  => false,
                'info'          => 'URL para onde o cliente será redirecionado após pagamento rejeitado',
            ], [
                'name'          => 'pending_url',
                'title'         => 'URL Pendente',
                'type'          => 'text',
                'validation'    => 'required|url',
                'channel_based' => false,
                'locale_based'  => false,
                'info'          => 'URL para onde o cliente será redirecionado quando pagamento estiver pendente',
            ], [
                'name'          => 'webhook_url',
                'title'         => 'URL do Webhook',
                'type'          => 'text',
                'validation'    => 'required|url',
                'channel_based' => false,
                'locale_based'  => false,
                'info'          => 'URL para receber notificações do Mercado Pago',
            ],
        ]
    ]
];