<?php
/**
 * SAW API v1 - Configurações
 * Data: 19/11/2025
 * Status: API em PHP puro
 */

// Configurações Gerais
define('API_VERSION', '1.0');
define('API_BASE_URL', '/SAW-main/api/v1/');

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Habilitando CORS e Headers JSON
if (php_sapi_name() !== 'cli') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Content-Type: application/json; charset=utf-8');

    // Preflight request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// Constantes do Banco de Dados
// Constantes do Banco de Dados
define('DB_HOST', getenv('DB_HOST') ?: '104.234.173.105');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'Ncm@647534');
define('DB_NAME', getenv('DB_NAME') ?: 'saw_quality');
define('DB_CHARSET', 'utf8mb4');

// Constantes de Resposta
define('API_SUCCESS', 200);
define('API_CREATED', 201);
define('API_BAD_REQUEST', 400);
define('API_UNAUTHORIZED', 401);
define('API_FORBIDDEN', 403);
define('API_NOT_FOUND', 404);
define('API_CONFLICT', 409);
define('API_INTERNAL_ERROR', 500);

// Tratamento de Erros
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/api_errors.log');

// Criar diretório de logs se não existir
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}
?>
