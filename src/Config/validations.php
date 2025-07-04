<?php

return [
    'mercadopago' => [
        'public_key' => 'required|string|min:10',
        'access_token' => 'required|string|min:10',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'image' => 'nullable|url|max:255',
        'sandbox_mode' => 'boolean',
        'active' => 'boolean',
    ],
]; 