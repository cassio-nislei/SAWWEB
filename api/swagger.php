<?php
/**
 * Swagger JSON Static Serve
 * Arquivo para servir swagger.json com headers CORS robustos
 * Sem passar pelo routing da API
 */

// Headers CORS - ANTES de qualquer output
header('Access-Control-Allow-Origin: *', true);
header('Access-Control-Allow-Methods: GET, OPTIONS, HEAD', true);
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization', true);
header('Access-Control-Max-Age: 86400', true);
header('Content-Type: application/json; charset=utf-8', true);
header('Content-Disposition: inline', true);
header('Cache-Control: public, max-age=300', true);

// Tratar OPTIONS (preflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Arquivo do swagger.json
$file = __DIR__ . '/swagger.json';

if (!file_exists($file)) {
    http_response_code(404);
    echo json_encode(['error' => 'swagger.json not found'], JSON_UNESCAPED_SLASHES);
    exit;
}

// Ler e servir arquivo
$content = file_get_contents($file);
if ($content === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not read file'], JSON_UNESCAPED_SLASHES);
    exit;
}

// Validar JSON
if (json_decode($content, true) === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid JSON'], JSON_UNESCAPED_SLASHES);
    exit;
}

// Servir
http_response_code(200);
echo $content;
exit;
?>
