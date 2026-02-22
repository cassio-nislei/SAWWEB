<?php
/**
 * Swagger Specification Endpoint
 * Arquivo separado para servir swagger.json com headers CORS completos
 * 
 * URL: /api/v1/swagger-spec.php
 * ou (via routing): GET /api/v1/swagger-spec
 */

// Headers CORS - COMPLETO
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH, HEAD');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, Content-Length');
header('Access-Control-Allow-Credentials: false');
header('Access-Control-Max-Age: 86400');

// Tipo de conteúdo
header('Content-Type: application/json; charset=utf-8');

// Cache
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0, public');
header('Pragma: no-cache');
header('Expires: 0');

// Handle OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Apenas GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Arquivo swagger.json
$swaggerFile = __DIR__ . '/../swagger.json';

// Validar existência
if (!file_exists($swaggerFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Swagger specification not found', 'file' => $swaggerFile]);
    exit;
}

// Ler arquivo
$content = file_get_contents($swaggerFile);

// Validar JSON
$decoded = json_decode($content, true);
if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid JSON', 'message' => json_last_error_msg()]);
    exit;
}

// Detectar servidor atual
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:7080';
$basePath = '/api/v1';
$currentServerUrl = $protocol . '://' . $host . $basePath;

// Atualizar servers dinamicamente
if (isset($decoded['servers']) && is_array($decoded['servers'])) {
    foreach ($decoded['servers'] as &$server) {
        // Se for localhost, usa URL atual
        if (strpos($currentServerUrl, 'localhost') !== false || strpos($currentServerUrl, '127.0.0.1') !== false) {
            if (strpos($server['description'] ?? '', 'Desenvolvimento') !== false || 
                strpos($server['description'] ?? '', 'Development') !== false) {
                $server['url'] = $currentServerUrl;
            }
        }
    }
}

// Log para debug
error_log("[SWAGGER-SPEC] GET request from " . $_SERVER['REMOTE_ADDR'] . " - returning " . strlen($content) . " bytes");

// Retornar JSON
http_response_code(200);
echo json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>
