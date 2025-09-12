<?php
// config.php - configuración del proyecto TransparenciaCiudadana

return [
    'db' => [
        'driver'   => 'pgsql',
        'host'     => '127.0.0.1',
        'port'     => '5432',
        'database' => 'tu_basedatos',
        'username' => 'tu_usuario',
        'password' => 'tu_contraseña',
        'charset'  => 'utf8'
    ],

    // Base URL del proyecto
    'base_url' => '/',

    // Configuración para integración con n8n
    'n8n' => [
        // URL del webhook (puedes cambiarla con variable de entorno)
        'webhook_url' => getenv('N8N_WEBHOOK_URL') ?: 'https://primary-production-c6f0f.up.railway.app/webhook-test/transparencia_webhook',
        // Tiempo de espera para las peticiones cURL a n8n
        'timeout' => getenv('N8N_TIMEOUT') ?: 10,
    ]
];
