<?php
/**
 * Swagger JSON Loader
 * Serve o arquivo swagger.json com headers CORS e cache corretos
 * Injeção dinâmica da baseURL correta
 */

// Headers CORS - Permitir requisições de qualquer origem
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, Content-Length');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Tipo de conteúdo e cache
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Arquivo swagger.json
$swaggerFile = __DIR__ . '/swagger.json';

if (!file_exists($swaggerFile)) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode([
        'error' => 'Swagger specification not found',
        'file' => $swaggerFile
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// Ler e servir o arquivo
$swaggerContent = file_get_contents($swaggerFile);

// Validar se é JSON válido
$decoded = json_decode($swaggerContent, true);
if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'error' => 'Invalid JSON in swagger.json',
        'message' => json_last_error_msg()
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// Detectar o URL base dinâmico
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:7080';
$basePath = '/api/v1';

// Construir a URL correta do servidor
$currentServerUrl = $protocol . '://' . $host . $basePath;

// Atualizar servers no swagger com a URL atual
if (isset($decoded['servers']) && is_array($decoded['servers'])) {
    // Encontrar o servidor de produção e atualizar sua URL
    foreach ($decoded['servers'] as &$server) {
        if (strpos($server['url'], '104.234.173.105') !== false || 
            strpos($server['description'], 'Produção') !== false ||
            strpos($server['description'], 'Production') !== false) {
            // Manter mas usar como fallback
            if (strpos($currentServerUrl, 'localhost') === false && 
                strpos($currentServerUrl, '127.0.0.1') === false) {
                $server['url'] = $currentServerUrl;
            }
        }
        // Se for localhost, usar a URL atual
        if (strpos($currentServerUrl, 'localhost') !== false || 
            strpos($currentServerUrl, '127.0.0.1') !== false) {
            if (strpos($server['description'], 'Desenvolvimento') !== false ||
                strpos($server['description'], 'Development') !== false) {
                $server['url'] = $currentServerUrl;
            }
        }
    }
}

// Se ainda não tem servers configurados dinamicamente, adicionar
if (!isset($decoded['servers']) || empty($decoded['servers'])) {
    $decoded['servers'] = [
        [
            'url' => $currentServerUrl,
            'description' => 'Servidor Atual'
        ]
    ];
}

// Servir o conteúdo com URLs dinamicamente atualizadas
echo json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>
