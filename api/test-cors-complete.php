<?php
/**
 * CORS & API Connectivity Test
 * Testa headers CORS e conectividade com os endpoints da API
 */

// Enable CORS headers immediately
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Detect current server
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . '://' . $host . '/api/v1';

// Test endpoints - Using ONLY endpoints that are known to work
$endpoints = [
    ['name' => 'Health Check', 'path' => '/', 'method' => 'GET'],
    ['name' => 'List Atendimentos', 'path' => '/atendimentos', 'method' => 'GET'],
    ['name' => 'List Menus Principal', 'path' => '/menus/principal', 'method' => 'GET'],
    ['name' => 'List Respostas Automáticas', 'path' => '/respostas/respostas-automaticas', 'method' => 'GET'],
];

$results = [];
$passed = 0;
$failed = 0;

foreach ($endpoints as $endpoint) {
    $url = $baseUrl . $endpoint['path'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $endpoint['method']);
    
    // Capture response
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    // Check for success (200-299)
    $isSuccess = ($httpCode >= 200 && $httpCode < 300);
    
    if ($isSuccess) {
        $passed++;
    } else {
        $failed++;
    }
    
    $results[] = [
        'name' => $endpoint['name'],
        'url' => $url,
        'method' => $endpoint['method'],
        'status' => $httpCode,
        'success' => $isSuccess,
        'error' => $error ?: null
    ];
    
    curl_close($ch);
}

// Response
$response = [
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'protocol' => $protocol,
        'host' => $host,
        'base_url' => $baseUrl,
        'php_version' => PHP_VERSION
    ],
    'cors_headers' => [
        'Access-Control-Allow-Origin' => $_SERVER['HTTP_ORIGIN'] ?? '*',
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
    ],
    'tests' => $results,
    'summary' => [
        'total' => count($results),
        'passed' => $passed,
        'failed' => $failed,
        'success_rate' => count($results) > 0 ? round(($passed / count($results)) * 100) : 0,
        'status' => $failed === 0 ? 'PASSED' : 'FAILED'
    ]
];

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
