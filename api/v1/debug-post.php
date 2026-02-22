<?php
/**
 * Debug - Testa recebimento de POST
 */

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$raw_input = file_get_contents('php://input');
$json_decoded = json_decode($raw_input, true);

$response = [
    'debug' => [
        'method' => $method,
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'not set',
        'raw_input_length' => strlen($raw_input),
        'raw_input' => $raw_input,
        'json_decoded' => $json_decoded,
        'json_error' => json_last_error_msg()
    ],
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
