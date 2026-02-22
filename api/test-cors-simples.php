<?php
/**
 * Simple CORS Test - Apenas verifica headers CORS
 * Este arquivo testa se os headers CORS estão sendo retornados corretamente
 */

// Headers CORS - Devem estar presentes
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Response simples
$response = [
    'status' => 'ok',
    'message' => 'CORS headers estão configurados corretamente',
    'timestamp' => date('Y-m-d H:i:s'),
    'headers_returned' => [
        'Access-Control-Allow-Origin' => $_SERVER['HTTP_ORIGIN'] ?? '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS, HEAD',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
    ]
];

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
