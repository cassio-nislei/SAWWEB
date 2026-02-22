<?php
/**
 * Arquivo de debug para testes
 */

header('Content-Type: application/json; charset=utf-8');

// Dados recebidos
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$input = file_get_contents('php://input');
$parsed = json_decode($input, true);

$debug = [
    'method' => $method,
    'path' => $path,
    'raw_input' => $input,
    'parsed_json' => $parsed,
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'não definido',
    '$_POST' => $_POST,
    '$_GET' => $_GET,
];

echo json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
