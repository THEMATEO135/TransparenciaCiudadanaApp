<?php
// config.php - configuración del proyecto TransparenciaCiudadana
return [
    'db_path' => __DIR__ . '/../data/transparencia.sqlite',
    // URL del webhook de n8n que recibirá los reportes (configura en tu instancia n8n)
    'n8n_webhook_url' => getenv('N8N_WEBHOOK_URL') ?: 'http://127.0.0.1:5678/webhook/transparencia_webhook',
    // Opcional: tiempo de espera en segundos para la petición cURL a n8n
    'n8n_timeout' => 10,
];
