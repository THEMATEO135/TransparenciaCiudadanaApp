<?php
// Configuración de headers CORS y JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejo de preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Cargar configuración
try {
    $config = require __DIR__ . '/config.php';
    $dbPath = $config['db_path'];
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de configuración']);
    exit;
}

// Validar input JSON
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Payload inválido, se esperaba JSON']);
    exit;
}

// Validar campos requeridos
$required = ['user_name', 'user_email', 'user_phone', 'service_type', 'description'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Campo requerido: $field"]);
        exit;
    }
}

// Sanitizar input
$user_name = filter_var(trim($input['user_name']), FILTER_SANITIZE_STRING);
$user_email = filter_var(trim($input['user_email']), FILTER_SANITIZE_EMAIL);
$user_phone = filter_var(trim($input['user_phone']), FILTER_SANITIZE_STRING);
$service_type = filter_var(trim($input['service_type']), FILTER_SANITIZE_STRING);
$description = filter_var(trim($input['description']), FILTER_SANITIZE_STRING);
$latitude = isset($input['latitude']) ? filter_var($input['latitude'], FILTER_VALIDATE_FLOAT) : null;
$longitude = isset($input['longitude']) ? filter_var($input['longitude'], FILTER_VALIDATE_FLOAT) : null;

try {
    // Conexión BD con manejo de errores mejorado
    $dsn = "sqlite:$dbPath";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, null, null, $options);

    // Transacción para asegurar consistencia
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'INSERT INTO reports (user_name, user_email, user_phone, service_type, description, latitude, longitude, created_at)
         VALUES (:user_name, :user_email, :user_phone, :service_type, :description, :latitude, :longitude, :created_at)'
    );

    $now = (new DateTime())->format(DateTime::ATOM);
    $stmt->execute([
        ':user_name' => $user_name,
        ':user_email' => $user_email,
        ':user_phone' => $user_phone,
        ':service_type' => $service_type,
        ':description' => $description,
        ':latitude' => $latitude,
        ':longitude' => $longitude,
        ':created_at' => $now
    ]);

    $reportId = $pdo->lastInsertId();
    $pdo->commit();

    // Preparar payload para n8n
    $payload = [
        'id' => (int)$reportId,
        'user_name' => $user_name,
        'user_email' => $user_email,
        'user_phone' => $user_phone,
        'service_type' => $service_type,
        'description' => $description,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'created_at' => $now
    ];

    // Configuración mejorada de cURL
    $ch = curl_init($config['n8n_webhook_url']);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $config['n8n_timeout'] ?? 10,
        CURLOPT_SSL_VERIFYPEER => false // Solo en desarrollo
    ]);

    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Respuesta al cliente
    echo json_encode([
        'ok' => true,
        'id' => (int)$reportId,
        'n8n' => [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response' => $resp,
            'error' => $err ?: null
        ]
    ]);

} catch (PDOException $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}
