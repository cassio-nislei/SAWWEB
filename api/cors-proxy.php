<?php
/**
 * CORS Proxy para API
 * Encaminha requisições para os endpoints da API com suporte completo a CORS
 */

// Headers CORS completos
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD, PATCH');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, Content-Length, X-API-Key');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
header('Vary: Origin');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Detectar URL base da API
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:7080';

// Path da API sem /cors-proxy.php
$requestPath = $_SERVER['REQUEST_URI'] ?? '/';
if (strpos($requestPath, '/api/cors-proxy.php') !== false) {
    $apiPath = str_replace('/api/cors-proxy.php', '', $requestPath);
} else {
    $apiPath = $requestPath;
}

// Construir URL completa do endpoint
$apiUrl = $protocol . '://' . $host . '/api/v1' . $apiPath;

// Preservar query string
if (!empty($_SERVER['QUERY_STRING'])) {
    $apiUrl .= '?' . $_SERVER['QUERY_STRING'];
}

// Preparar headers para forwarding
$headers = [];
foreach (getallheaders() as $name => $value) {
    // Skip host-related headers
    if (!in_array(strtolower($name), ['host', 'connection', 'content-length'])) {
        $headers[] = $name . ': ' . $value;
    }
}

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Set request method and data
$method = $_SERVER['REQUEST_METHOD'];
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
    $data = file_get_contents('php://input');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
}

if (!empty($headers)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
}

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Return response
header('HTTP/1.1 ' . $httpCode);

// Log the request
error_log("[API Proxy] $method $apiUrl - HTTP $httpCode");

if ($error) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Proxy error',
        'message' => $error,
        'url_attempted' => $apiUrl
    ]);
} else {
    header('Content-Type: application/json');
    echo $response;
}
?>
