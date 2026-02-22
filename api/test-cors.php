<?php
/**
 * CORS Test Script
 * Testa conectividade e CORS da API
 */

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Test data
$tests = [
    'health' => [
        'name' => 'Health Check',
        'url' => '/api/v1/',
        'method' => 'GET'
    ],
    'login' => [
        'name' => 'Login Endpoint',
        'url' => '/api/v1/auth/login',
        'method' => 'POST',
        'body' => json_encode(['login' => 'admin', 'senha' => '123456'])
    ],
    'atendimentos' => [
        'name' => 'List Atendimentos',
        'url' => '/api/v1/atendimentos',
        'method' => 'GET'
    ],
    'parametros' => [
        'name' => 'Sistema Parameters',
        'url' => '/api/v1/parametros/sistema',
        'method' => 'GET'
    ]
];

// Detect base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:7080';

$results = [];

foreach ($tests as $key => $test) {
    $url = $protocol . '://' . $host . $test['url'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    if ($test['method'] === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if (isset($test['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $test['body']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $results[$key] = [
        'name' => $test['name'],
        'url' => $url,
        'method' => $test['method'],
        'status' => $httpCode,
        'success' => $httpCode >= 200 && $httpCode < 300,
        'error' => $error ?: null,
        'response_length' => strlen($response) ?? 0
    ];
}

// Output results
echo json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => [
        'protocol' => $protocol,
        'host' => $host,
        'base_url' => $protocol . '://' . $host . '/api/v1'
    ],
    'tests' => $results,
    'summary' => [
        'total' => count($results),
        'passed' => count(array_filter($results, fn($r) => $r['success'])),
        'failed' => count(array_filter($results, fn($r) => !$r['success']))
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
